<?php

namespace App\Http\Controllers;

use App\Events\MenuAvailabilityChanged;
use App\Events\OrderItemCancelled;
use App\Events\OrderStatusUpdated;
use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KdsController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['items.modifiers', 'items.menu.category', 'table', 'user'])
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->oldest() // FIFO: Pesanan terlama masuk berada di paling depan
            ->get();

        $menus = Menu::with('category')->orderBy('name')->get();

        $settings = Setting::pluck('value', 'key')->toArray();

        $warningMinutes = (int) ($settings['kds_warning_time_min'] ?? 10);
        $dangerMinutes = (int) ($settings['kds_danger_time_min'] ?? 20);

        return view('koki_dashboard.index', compact(
            'orders',
            'menus',
            'settings',
            'warningMinutes',
            'dangerMinutes'
        ));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled',
        ]);

        $previousStatus = $order->status;
        $newStatus = $request->status;

        $order->status = $newStatus;
        $order->save();

        // Update status item pesanan jika status order berubah
        if ($newStatus === 'processing') {
            $order->items()->where('status', 'pending')->update(['status' => 'cooking']);
        } elseif ($newStatus === 'ready') {
            $order->items()->whereIn('status', ['pending', 'cooking'])->update(['status' => 'ready']);
        } elseif ($newStatus === 'completed') {
            $order->items()->update(['status' => 'served']);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'KDS_STATUS_UPDATE',
            'description' => "Status pesanan #{$order->invoice_number} diubah dari {$previousStatus} menjadi {$newStatus} oleh Koki",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Broadcast ke Reverb channel kitchen & pos
        try {
            broadcast(new OrderStatusUpdated($order, $previousStatus, $newStatus));
        } catch (\Throwable $e) {
            // Fail-safe jika websocket daemon belum aktif
        }

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->invoice_number} berhasil diubah menjadi {$newStatus}.",
            'order' => $order->load(['items.modifiers', 'table']),
        ]);
    }

    public function updateItemStatus(Request $request, OrderItem $item): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,ready,served,cancelled',
        ]);

        $item->status = $request->status;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => "Item '{$item->menu_name}' ditandai {$item->status}.",
            'item' => $item,
        ]);
    }

    public function cancelItem(Request $request, OrderItem $item): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $item->status = 'cancelled';
        $item->cancel_reason = $request->reason;
        $item->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'KDS_CANCEL_ITEM',
            'description' => "Koki membatalkan item {$item->menu_name} (Order #{$item->order->invoice_number}): {$request->reason}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            broadcast(new OrderItemCancelled($item, $request->reason));
        } catch (\Throwable $e) {
            // fail-safe
        }

        return response()->json([
            'success' => true,
            'message' => "Item '{$item->menu_name}' berhasil dibatalkan.",
            'item' => $item,
        ]);
    }

    public function toggleSoldOut(Request $request, Menu $menu): JsonResponse
    {
        $menu->is_available = !$menu->is_available;
        $menu->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'KDS_TOGGLE_SOLDOUT',
            'description' => "Koki mengubah ketersediaan menu {$menu->name} menjadi: " . ($menu->is_available ? 'TERSEDIA' : 'HABIS (SOLD OUT)'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            broadcast(new MenuAvailabilityChanged($menu->id, $menu->is_available, $menu->name));
        } catch (\Throwable $e) {
            // fail-safe
        }

        return response()->json([
            'success' => true,
            'is_available' => $menu->is_available,
            'message' => "Menu {$menu->name} berhasil ditandai " . ($menu->is_available ? 'TERSEDIA' : 'SOLD OUT') . ".",
        ]);
    }
}
