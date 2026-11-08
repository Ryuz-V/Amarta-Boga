<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CashierModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $cashier;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier = User::where('email', 'kasir@amartaboga.com')->first();
        $this->admin = User::where('email', 'admin@amartaboga.com')->first();
    }

    public function test_cashier_can_access_pos_screen(): void
    {
        $response = $this->actingAs($this->cashier)->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('Order Line');
        $response->assertSee('Current Order');
    }

    public function test_cashier_can_checkout_cash_order_with_valid_calculation(): void
    {
        $menu = Menu::where('is_available', true)->first();
        $table = Table::first();

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Budi Santoso',
            'order_type' => 'dine_in',
            'table_id' => $table->id,
            'payment_method' => 'cash',
            'cash_tendered' => 300000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 2,
                    'notes' => 'Tanpa kecap',
                ]
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'order_type' => 'dine_in',
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_method' => 'cash',
            'status' => 'success',
        ]);
    }

    public function test_server_rejects_checkout_if_cash_tendered_is_insufficient(): void
    {
        $menu = Menu::where('is_available', true)->first();
        $table = Table::first();

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Andi Kurang',
            'order_type' => 'dine_in',
            'table_id' => $table->id,
            'payment_method' => 'cash',
            'cash_tendered' => 5000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_cashier_can_checkout_order_with_qris(): void
    {
        $menu = Menu::where('is_available', true)->first();

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Siti QRIS',
            'order_type' => 'takeaway',
            'payment_method' => 'qris',
            'reference_number' => 'QRIS-TEST-9988',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Siti QRIS',
            'order_type' => 'take_away',
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_method' => 'qris',
            'reference_number' => 'QRIS-TEST-9988',
        ]);
    }

    public function test_cashier_can_checkout_with_split_bill(): void
    {
        $menu = Menu::where('is_available', true)->first();

        // Subtotal + tax + service calculation
        $expectedGrandTotal = (float) $menu->price * 1;
        // With tax 11% and service 5%
        $tax = $expectedGrandTotal * 0.11;
        $service = $expectedGrandTotal * 0.05;
        $grandTotal = round($expectedGrandTotal + $tax + $service);

        $part1 = ceil($grandTotal / 2);
        $part2 = $grandTotal - $part1;

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Rombongan Split',
            'order_type' => 'take_away',
            'payment_method' => 'split',
            'split_payments' => [
                ['method' => 'cash', 'amount' => $part1],
                ['method' => 'qris', 'amount' => $part2, 'reference' => 'QRIS-SPLIT-01'],
            ],
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Rombongan Split',
        ]);
    }

    public function test_checkout_deducts_ingredient_stock_based_on_recipes(): void
    {
        // Cari menu yang punya resep terdaftar
        $menu = Menu::has('recipes')->first();
        if (!$menu) {
            $this->markTestSkipped('Tidak ada menu dengan resep terdaftar.');
        }

        $recipe = $menu->recipes()->first();
        $ingredient = $recipe->ingredient;
        $initialStock = (float) $ingredient->current_stock;
        $orderQty = 2;
        $expectedDeduction = (float) $recipe->quantity * $orderQty;

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Resep Stock Test',
            'order_type' => 'take_away',
            'payment_method' => 'cash',
            'cash_tendered' => 500000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => $orderQty,
                ]
            ],
        ]);

        $response->assertStatus(200);
        $ingredient->refresh();

        $this->assertEquals($initialStock - $expectedDeduction, (float) $ingredient->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $ingredient->id,
            'type' => 'order_deduction',
            'quantity' => $expectedDeduction,
        ]);
    }

    public function test_cashier_can_view_thermal_receipt(): void
    {
        $order = Order::first();
        $response = $this->actingAs($this->cashier)->get("/pos/receipt/{$order->id}");
        $response->assertStatus(200);
        $response->assertSee($order->invoice_number);
        $response->assertSee('AMARTA BOGA');
    }

    public function test_cashier_can_download_receipt_pdf(): void
    {
        $order = Order::first();
        $response = $this->actingAs($this->cashier)->get("/pos/receipt/{$order->id}/pdf");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_cashier_can_view_orders_history(): void
    {
        $response = $this->actingAs($this->cashier)->get('/pos/orders');
        $response->assertStatus(200);
        $response->assertSee('Semua Transaksi');
    }
}
