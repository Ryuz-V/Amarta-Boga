<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemModifier;
use App\Models\Payment;
use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleOrderSeeder extends Seeder
{
    public function run(): void
    {
        $kasir = User::role('staff')->first();
        if (!$kasir) {
            return;
        }

        // Create an open shift for the cashier
        $shift = Shift::firstOrCreate(
            ['user_id' => $kasir->id, 'status' => 'open'],
            [
                'start_time' => now()->startOfDay()->addHours(8), // Started at 08:00 AM
                'starting_cash' => 500000.00, // Rp 500.000 modal awal
            ]
        );

        $table01 = Table::where('number', '01')->first();
        $table02 = Table::where('number', '02')->first();
        $table03 = Table::where('number', '03')->first(); // Occupied
        $tableVIP = Table::where('number', 'VIP-1')->first();

        $menuWagyu = Menu::where('slug', 'wagyu-beef-truffle-donburi')->first();
        $menuNasgor = Menu::where('slug', 'nasi-goreng-wagyu-kecombrang')->first();
        $menuKopi   = Menu::where('slug', 'kopi-susu-gula-aren')->first();
        $menuMatcha = Menu::where('slug', 'matcha-molten-lava-cake')->first();
        $menuCurry  = Menu::where('slug', 'chicken-katsu-curry-rice')->first();

        // Transaction 1: Completed Order (Meja 01, Cash)
        if ($menuWagyu && $menuKopi) {
            DB::transaction(function () use ($kasir, $shift, $table01, $menuWagyu, $menuKopi) {
                $subtotal = ($menuWagyu->price * 2) + ($menuKopi->price * 2); // 176k + 56k = 232k
                $tax = $subtotal * 0.10; // 23.2k
                $service = $subtotal * 0.05; // 11.6k
                $total = $subtotal + $tax + $service; // 266.8k
                $rounding = 200; // Round to 267.000
                $grandTotal = $total + $rounding;

                $order1 = Order::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-0001',
                    'user_id' => $kasir->id,
                    'table_id' => $table01?->id,
                    'shift_id' => $shift->id,
                    'customer_name' => 'Dimas Anggara',
                    'order_type' => 'dine_in',
                    'status' => 'completed',
                    'subtotal' => $subtotal,
                    'tax_rate' => 10.00,
                    'tax_amount' => $tax,
                    'service_charge_rate' => 5.00,
                    'service_charge' => $service,
                    'rounding' => $rounding,
                    'total_amount' => $grandTotal,
                    'paid_amount' => 300000.00,
                    'change_amount' => 300000.00 - $grandTotal,
                    'payment_status' => 'paid',
                    'notes' => 'Tamu request sendok garpu tambahan',
                ]);

                // Order items
                $item1 = OrderItem::create([
                    'order_id' => $order1->id,
                    'menu_id' => $menuWagyu->id,
                    'menu_name' => $menuWagyu->name,
                    'unit_price' => $menuWagyu->price,
                    'cost_price' => $menuWagyu->cost_price,
                    'quantity' => 2,
                    'modifiers_price' => 0,
                    'subtotal' => $menuWagyu->price * 2,
                    'notes' => 'Tingkat kematangan Medium Rare',
                    'status' => 'served',
                ]);

                $item2 = OrderItem::create([
                    'order_id' => $order1->id,
                    'menu_id' => $menuKopi->id,
                    'menu_name' => $menuKopi->name,
                    'unit_price' => $menuKopi->price,
                    'cost_price' => $menuKopi->cost_price,
                    'quantity' => 2,
                    'modifiers_price' => 0,
                    'subtotal' => $menuKopi->price * 2,
                    'notes' => 'Less ice, normal sweet',
                    'status' => 'served',
                ]);

                // Payment record
                Payment::create([
                    'order_id' => $order1->id,
                    'user_id' => $kasir->id,
                    'payment_method' => 'cash',
                    'amount' => $grandTotal,
                    'status' => 'success',
                    'notes' => 'Uang tunai Rp 300.000',
                ]);
            });
        }

        // Transaction 2: Completed Order with Discount (Meja 02, QRIS)
        if ($menuNasgor && $menuCurry) {
            DB::transaction(function () use ($kasir, $shift, $table02, $menuNasgor, $menuCurry) {
                $discount = Discount::where('code', 'HEMAT10')->first();
                $subtotal = ($menuNasgor->price * 2) + $menuCurry->price; // 130k + 52k = 182k
                $discAmount = $discount ? $discount->calculateDiscount($subtotal) : 0; // 18.2k
                $afterDisc = $subtotal - $discAmount; // 163.8k
                $tax = $afterDisc * 0.10; // 16.38k
                $service = $afterDisc * 0.05; // 8.19k
                $grandTotal = $afterDisc + $tax + $service; // 188.37k

                $order2 = Order::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-0002',
                    'user_id' => $kasir->id,
                    'table_id' => $table02?->id,
                    'shift_id' => $shift->id,
                    'customer_name' => 'Clara Novita',
                    'order_type' => 'dine_in',
                    'status' => 'completed',
                    'subtotal' => $subtotal,
                    'discount_id' => $discount?->id,
                    'discount_amount' => $discAmount,
                    'tax_rate' => 10.00,
                    'tax_amount' => $tax,
                    'service_charge_rate' => 5.00,
                    'service_charge' => $service,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $grandTotal,
                    'change_amount' => 0,
                    'payment_status' => 'paid',
                ]);

                OrderItem::create([
                    'order_id' => $order2->id,
                    'menu_id' => $menuNasgor->id,
                    'menu_name' => $menuNasgor->name,
                    'unit_price' => $menuNasgor->price,
                    'cost_price' => $menuNasgor->cost_price,
                    'quantity' => 2,
                    'modifiers_price' => 0,
                    'subtotal' => $menuNasgor->price * 2,
                    'notes' => 'Level 2 Pedas Nampol, jangan pakai daun bawang',
                    'status' => 'served',
                ]);

                OrderItem::create([
                    'order_id' => $order2->id,
                    'menu_id' => $menuCurry->id,
                    'menu_name' => $menuCurry->name,
                    'unit_price' => $menuCurry->price,
                    'cost_price' => $menuCurry->cost_price,
                    'quantity' => 1,
                    'modifiers_price' => 0,
                    'subtotal' => $menuCurry->price,
                    'status' => 'served',
                ]);

                Payment::create([
                    'order_id' => $order2->id,
                    'user_id' => $kasir->id,
                    'payment_method' => 'qris',
                    'amount' => $grandTotal,
                    'reference_number' => 'QRIS-NMID-98213812',
                    'status' => 'success',
                ]);
            });
        }

        // Transaction 3: Active "Processing" Order (Meja 03, for Kitchen Display System & Order Line testing)
        if ($menuWagyu && $menuNasgor && $menuMatcha) {
            DB::transaction(function () use ($kasir, $shift, $table03, $menuWagyu, $menuNasgor, $menuMatcha) {
                $subtotal = $menuWagyu->price + $menuNasgor->price + $menuMatcha->price;
                $tax = $subtotal * 0.10;
                $service = $subtotal * 0.05;
                $total = $subtotal + $tax + $service;

                $order3 = Order::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-0003',
                    'user_id' => $kasir->id,
                    'table_id' => $table03?->id,
                    'shift_id' => $shift->id,
                    'customer_name' => 'Faris Pratama',
                    'order_type' => 'dine_in',
                    'status' => 'processing', // Sedang Dimasak di KDS!
                    'subtotal' => $subtotal,
                    'tax_rate' => 10.00,
                    'tax_amount' => $tax,
                    'service_charge_rate' => 5.00,
                    'service_charge' => $service,
                    'total_amount' => $total,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'payment_status' => 'unpaid',
                    'notes' => 'Pesanan VIP tamu kantor, dahulukan dessertnya setelah main course',
                ]);

                $orderItem1 = OrderItem::create([
                    'order_id' => $order3->id,
                    'menu_id' => $menuWagyu->id,
                    'menu_name' => $menuWagyu->name,
                    'unit_price' => $menuWagyu->price,
                    'cost_price' => $menuWagyu->cost_price,
                    'quantity' => 1,
                    'modifiers_price' => 6000,
                    'subtotal' => $menuWagyu->price + 6000,
                    'notes' => 'ALERGI KACANG. Onsen egg matang sempurna.',
                    'status' => 'cooking',
                ]);

                OrderItemModifier::create([
                    'order_item_id' => $orderItem1->id,
                    'type' => 'addon',
                    'name' => 'Extra Onsen Egg',
                    'price' => 6000,
                ]);

                OrderItem::create([
                    'order_id' => $order3->id,
                    'menu_id' => $menuNasgor->id,
                    'menu_name' => $menuNasgor->name,
                    'unit_price' => $menuNasgor->price,
                    'cost_price' => $menuNasgor->cost_price,
                    'quantity' => 1,
                    'modifiers_price' => 0,
                    'subtotal' => $menuNasgor->price,
                    'notes' => 'Level 1 Sedang',
                    'status' => 'cooking',
                ]);

                OrderItem::create([
                    'order_id' => $order3->id,
                    'menu_id' => $menuMatcha->id,
                    'menu_name' => $menuMatcha->name,
                    'unit_price' => $menuMatcha->price,
                    'cost_price' => $menuMatcha->cost_price,
                    'quantity' => 1,
                    'modifiers_price' => 0,
                    'subtotal' => $menuMatcha->price,
                    'notes' => 'Keluarkan saat main course selesai',
                    'status' => 'pending',
                ]);
            });
        }
    }
}
