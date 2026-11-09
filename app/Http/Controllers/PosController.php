<?php

namespace App\Http\Controllers;

use App\Events\LowStockAlert;
use App\Events\OrderCreated;
use App\Http\Requests\CheckoutOrderRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuAddon;
use App\Models\MenuRecipe;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemModifier;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $menus = Menu::with(['variants', 'addons', 'category', 'recipes.ingredient'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $activeOrders = Order::with(['table', 'items.modifiers'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->take(8)
            ->get();

        $tables = Table::orderBy('number')->get();

        $activeShift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        $discounts = Discount::where('is_active', true)->get();

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('pos.index', compact(
            'categories',
            'menus',
            'activeOrders',
            'tables',
            'activeShift',
            'discounts',
            'settings'
        ));
    }

    public function checkout(CheckoutOrderRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $settings = Setting::pluck('value', 'key')->toArray();

            // 1. Verifikasi dan Kalkulasi Server-Side untuk setiap item
            $calculatedItems = [];
            $subtotal = 0;

            foreach ($request->items as $cartItem) {
                $menu = Menu::with(['variants', 'addons', 'recipes.ingredient'])->findOrFail($cartItem['menu_id']);

                if (!$menu->is_active || !$menu->is_available) {
                    throw new \Exception("Menu '{$menu->name}' saat ini tidak tersedia / sold out.");
                }

                $qty = (int) $cartItem['quantity'];
                $unitPrice = (float) $menu->price;
                $costPrice = (float) $menu->cost_price;
                $modifiersPrice = 0;
                $selectedModifiers = [];

                // Cek Varian
                if (!empty($cartItem['variant_id'])) {
                    $variant = MenuVariant::where('menu_id', $menu->id)->find($cartItem['variant_id']);
                    if ($variant) {
                        $modifiersPrice += (float) $variant->price_adjustment;
                        $selectedModifiers[] = [
                            'type' => 'variant',
                            'name' => $variant->name,
                            'price' => (float) $variant->price_adjustment,
                        ];
                    }
                }

                // Cek Add-ons
                if (!empty($cartItem['addon_ids']) && is_array($cartItem['addon_ids'])) {
                    $addons = MenuAddon::where('menu_id', $menu->id)->whereIn('id', $cartItem['addon_ids'])->get();
                    foreach ($addons as $addon) {
                        $modifiersPrice += (float) $addon->price;
                        $selectedModifiers[] = [
                            'type' => 'addon',
                            'name' => $addon->name,
                            'price' => (float) $addon->price,
                        ];
                    }
                }

                $itemSubtotal = ($unitPrice + $modifiersPrice) * $qty;
                $subtotal += $itemSubtotal;

                $calculatedItems[] = [
                    'menu' => $menu,
                    'menu_name' => $menu->name,
                    'unit_price' => $unitPrice,
                    'cost_price' => $costPrice,
                    'quantity' => $qty,
                    'modifiers_price' => $modifiersPrice,
                    'subtotal' => $itemSubtotal,
                    'notes' => $cartItem['notes'] ?? null,
                    'modifiers' => $selectedModifiers,
                ];
            }

            // 2. Kalkulasi Diskon / Voucher
            $discountId = null;
            $discountAmount = 0;

            if (!empty($request->discount_code)) {
                $discount = Discount::where('code', $request->discount_code)
                    ->where('is_active', true)
                    ->first();

                if ($discount) {
                    // Validasi minimal transaksi
                    if ($discount->min_spend && $subtotal < (float) $discount->min_spend) {
                        throw new \Exception("Kode diskon {$discount->code} membutuhkan minimal belanja Rp " . number_format($discount->min_spend, 0, ',', '.'));
                    }

                    // Validasi masa aktif
                    $now = now();
                    if ($discount->start_date && $now->lt($discount->start_date)) {
                        throw new \Exception("Kode diskon {$discount->code} belum berlaku.");
                    }
                    if ($discount->end_date && $now->gt($discount->end_date)) {
                        throw new \Exception("Kode diskon {$discount->code} sudah kedaluwarsa.");
                    }

                    $discountId = $discount->id;
                    if ($discount->type === 'percentage') {
                        $discountAmount = ($subtotal * (float) $discount->value) / 100;
                        if ($discount->max_discount && $discountAmount > (float) $discount->max_discount) {
                            $discountAmount = (float) $discount->max_discount;
                        }
                    } else {
                        $discountAmount = (float) $discount->value;
                    }
                }
            }

            $discountAmount = min($subtotal, $discountAmount);
            $amountAfterDiscount = max(0, $subtotal - $discountAmount);

            // 3. Kalkulasi Pajak & Service Charge
            $taxRate = (!empty($settings['tax_enabled']) && $settings['tax_enabled'] == '1')
                ? (float) ($settings['tax_percentage'] ?? 11)
                : 0;
            $taxAmount = ($amountAfterDiscount * $taxRate) / 100;

            $serviceRate = (!empty($settings['service_charge_enabled']) && $settings['service_charge_enabled'] == '1')
                ? (float) ($settings['service_charge_percentage'] ?? 5)
                : 0;
            $serviceAmount = ($amountAfterDiscount * $serviceRate) / 100;

            $grandTotal = round($amountAfterDiscount + $taxAmount + $serviceAmount);

            // 4. Validasi Pembayaran
            $paymentMethod = $request->payment_method;
            $paidAmount = 0;
            $changeAmount = 0;
            $paymentEntries = [];

            if ($paymentMethod === 'cash') {
                $cashTendered = (float) ($request->cash_tendered ?? 0);
                if ($cashTendered < $grandTotal) {
                    throw new \Exception("Uang tunai kurang. Total tagihan Rp " . number_format($grandTotal, 0, ',', '.') . ", diberikan Rp " . number_format($cashTendered, 0, ',', '.'));
                }
                $paidAmount = $cashTendered;
                $changeAmount = $cashTendered - $grandTotal;

                $paymentEntries[] = [
                    'payment_method' => 'cash',
                    'amount' => $grandTotal,
                    'reference_number' => null,
                    'status' => 'success',
                ];
            } elseif ($paymentMethod === 'split') {
                if (empty($request->split_payments) || !is_array($request->split_payments)) {
                    throw new \Exception("Data split bill pembayaran belum lengkap.");
                }

                $splitTotal = 0;
                foreach ($request->split_payments as $sp) {
                    $spAmount = (float) $sp['amount'];
                    $splitTotal += $spAmount;
                    $method = in_array($sp['method'], ['debit', 'credit', 'card']) ? 'card' : $sp['method'];
                    $paymentEntries[] = [
                        'payment_method' => $method,
                        'amount' => $spAmount,
                        'reference_number' => $sp['reference'] ?? null,
                        'status' => 'success',
                    ];
                }

                if ($splitTotal < $grandTotal) {
                    throw new \Exception("Total split pembayaran Rp " . number_format($splitTotal, 0, ',', '.') . " belum mencukupi total tagihan Rp " . number_format($grandTotal, 0, ',', '.'));
                }

                $paidAmount = $splitTotal;
                $changeAmount = max(0, $splitTotal - $grandTotal);
            } else {
                // QRIS, Debit, Credit
                $paidAmount = $grandTotal;
                $changeAmount = 0;
                $method = in_array($paymentMethod, ['debit', 'credit', 'card']) ? 'card' : $paymentMethod;
                $paymentEntries[] = [
                    'payment_method' => $method,
                    'amount' => $grandTotal,
                    'reference_number' => $request->reference_number ?? ('REF-' . strtoupper(Str::random(8))),
                    'status' => 'success',
                ];
            }

            // 5. Generate Invoice Number & Ambil Shift Aktif
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $orderType = ($request->order_type === 'takeaway') ? 'take_away' : $request->order_type;

            $activeShift = Shift::where('user_id', $user->id)
                ->where('status', 'open')
                ->latest()
                ->first();

            // 6. Buat Record Order
            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'table_id' => $orderType === 'dine_in' ? $request->table_id : null,
                'shift_id' => $activeShift?->id,
                'customer_name' => $request->customer_name,
                'order_type' => $orderType,
                'status' => 'processing',
                'subtotal' => $subtotal,
                'discount_id' => $discountId,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'service_charge_rate' => $serviceRate,
                'service_charge' => $serviceAmount,
                'rounding' => 0,
                'total_amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_status' => 'paid',
                'notes' => $request->notes,
                'is_held' => false,
            ]);

            // Jika Dine In, ubah status Meja menjadi 'occupied'
            if ($orderType === 'dine_in' && $request->table_id) {
                Table::where('id', $request->table_id)->update(['status' => 'occupied']);
            }

            // 7. Simpan Order Items, Modifiers, & Pengurangan Stok Resep
            foreach ($calculatedItems as $itemData) {
                $orderItem = $order->items()->create([
                    'menu_id' => $itemData['menu']->id,
                    'menu_name' => $itemData['menu_name'],
                    'unit_price' => $itemData['unit_price'],
                    'cost_price' => $itemData['cost_price'],
                    'quantity' => $itemData['quantity'],
                    'modifiers_price' => $itemData['modifiers_price'],
                    'subtotal' => $itemData['subtotal'],
                    'notes' => $itemData['notes'],
                    'status' => 'pending',
                ]);

                // Simpan modifiers
                foreach ($itemData['modifiers'] as $mod) {
                    $orderItem->modifiers()->create([
                        'type' => $mod['type'],
                        'name' => $mod['name'],
                        'price' => $mod['price'],
                    ]);
                }

                // Pengurangan Stok Bahan Berdasarkan Resep
                foreach ($itemData['menu']->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if ($ingredient) {
                        $deductQty = (float) $recipe->quantity * (float) $itemData['quantity'];
                        $prevStock = (float) $ingredient->current_stock;
                        $newStock = max(0, $prevStock - $deductQty);

                        $ingredient->current_stock = $newStock;
                        $ingredient->save();

                        StockMovement::create([
                            'ingredient_id' => $ingredient->id,
                            'user_id' => $user->id,
                            'type' => 'order_deduction',
                            'quantity' => $deductQty,
                            'previous_stock' => $prevStock,
                            'current_stock' => $newStock,
                            'notes' => "Penjualan menu {$itemData['menu_name']} (#{$order->invoice_number})",
                        ]);

                        // Alert jika stok menipis
                        if ($newStock <= (float) $ingredient->minimum_stock) {
                            try {
                                broadcast(new LowStockAlert($ingredient));
                            } catch (\Throwable $e) {
                                // broadcast fail-safe
                            }
                        }
                    }
                }
            }

            // 8. Simpan Record Pembayaran
            foreach ($paymentEntries as $pe) {
                $order->payments()->create([
                    'user_id' => $user->id,
                    'payment_method' => $pe['payment_method'],
                    'amount' => $pe['amount'],
                    'reference_number' => $pe['reference_number'],
                    'status' => $pe['status'] ?? 'success',
                ]);
            }

            // 9. Activity Log
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'CHECKOUT_ORDER',
                'description' => "Memproses pesanan #{$order->invoice_number} tamu {$order->customer_name} (Rp " . number_format($order->total_amount, 0, ',', '.') . ") metode: {$paymentMethod}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 10. Broadcast Reverb ke Dapur & Kasir
            try {
                broadcast(new OrderCreated($order));
            } catch (\Throwable $e) {
                // fail-safe jika websocket daemon belum aktif
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pesanan #{$order->invoice_number} berhasil diproses!",
                'order' => [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'paid_amount' => $order->paid_amount,
                    'change_amount' => $order->change_amount,
                    'payment_method' => $paymentMethod,
                ],
                'receipt_url' => route('pos.receipt', $order),
                'pdf_url' => route('pos.receipt.pdf', $order),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function receipt(Order $order): View
    {
        $order->load(['items.modifiers', 'table', 'user', 'payments', 'discount']);
        $settings = Setting::pluck('value', 'key')->toArray();
        $paperWidth = $settings['receipt_paper_width'] ?? '80mm';

        return view('pos.receipt', compact('order', 'settings', 'paperWidth'));
    }

    public function receiptPdf(Order $order): Response
    {
        $order->load(['items.modifiers', 'table', 'user', 'payments', 'discount']);
        $settings = Setting::pluck('value', 'key')->toArray();
        $paperWidth = $settings['receipt_paper_width'] ?? '80mm';

        // 80mm ~ 226 pt, 58mm ~ 164 pt
        $widthPt = ($paperWidth === '58mm') ? 164 : 226;
        $heightPt = 800; // roll receipt continuous height

        $pdf = Pdf::loadView('pos.receipt-pdf', compact('order', 'settings', 'paperWidth'))
            ->setPaper([0, 0, $widthPt, $heightPt], 'portrait');

        return $pdf->download("struk-{$order->invoice_number}.pdf");
    }

    public function orders(Request $request)
    {
        $orders = Order::with(['table', 'user', 'payments', 'items'])
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        }

        return view('pos.orders', compact('orders'));
    }

    public function holdOrder(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $invoiceNumber = 'HLD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'table_id' => $request->table_id,
                'customer_name' => $request->customer_name,
                'order_type' => $request->order_type ?? 'dine_in',
                'status' => 'pending',
                'subtotal' => (float) ($request->subtotal ?? 0),
                'total_amount' => (float) ($request->total_amount ?? 0),
                'payment_status' => 'unpaid',
                'is_held' => true,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $orderItem = $order->items()->create([
                    'menu_id' => $item['menu_id'],
                    'menu_name' => $item['name'] ?? 'Menu',
                    'unit_price' => (float) ($item['price'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? $item['qty'] ?? 1),
                    'subtotal' => (float) (($item['price'] ?? 0) * ($item['quantity'] ?? $item['qty'] ?? 1)),
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending',
                ]);

                if (!empty($item['addon_ids'])) {
                    foreach ($item['addon_ids'] as $addonId) {
                        $orderItem->modifiers()->create([
                            'type' => 'addon',
                            'name' => 'Addon #' . $addonId,
                            'price' => 0,
                        ]);
                    }
                }
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'HOLD_ORDER',
                'description' => "Kasir menahan pesanan #{$order->invoice_number} tamu {$order->customer_name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pesanan #{$order->invoice_number} berhasil ditahan (parkir).",
                'order' => $order->load('items'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menahan pesanan: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function heldOrders(): JsonResponse
    {
        $orders = Order::with(['items.modifiers', 'table'])
            ->where('is_held', true)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    public function resumeOrder(Order $order): JsonResponse
    {
        $order->load(['items.modifiers', 'table']);
        $orderData = $order->toArray();

        // Hapus pesanan held dari antrean
        $order->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dipanggil kembali ke keranjang.',
            'order' => $orderData,
        ]);
    }

    public function voidOrder(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        $order->status = 'cancelled';
        $order->cancel_reason = $request->cancel_reason;
        $order->cancelled_by = Auth::id();
        $order->save();

        if ($order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'available']);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'VOID_ORDER',
            'description' => "Kasir membatalkan/void pesanan #{$order->invoice_number}: {$request->cancel_reason}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pesanan #{$order->invoice_number} berhasil dibatalkan (void).",
        ]);
    }
}
