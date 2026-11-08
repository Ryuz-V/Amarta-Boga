<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KdsModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $koki;
    protected User $cashier;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->koki = User::where('email', 'koki@amartaboga.com')->first();
        $this->cashier = User::where('email', 'kasir@amartaboga.com')->first();
        $this->admin = User::where('email', 'admin@amartaboga.com')->first();
    }

    public function test_koki_can_access_kds_screen(): void
    {
        $response = $this->actingAs($this->koki)->get('/kds');
        $response->assertStatus(200);
        $response->assertSee('Kitchen Display System');
        $response->assertSee('Semua Stasiun');
    }

    public function test_staff_kasir_cannot_access_kds(): void
    {
        $response = $this->actingAs($this->cashier)->get('/kds');
        $response->assertStatus(403);
    }

    public function test_koki_can_advance_order_status(): void
    {
        $order = Order::first();

        // 1. Mulai Masak (processing)
        $response1 = $this->actingAs($this->koki)->patchJson("/kds/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response1->assertStatus(200);
        $response1->assertJson(['success' => true]);
        $order->refresh();
        $this->assertEquals('processing', $order->status);

        // 2. Siap Saji (ready)
        $response2 = $this->actingAs($this->koki)->patchJson("/kds/orders/{$order->id}/status", [
            'status' => 'ready',
        ]);

        $response2->assertStatus(200);
        $order->refresh();
        $this->assertEquals('ready', $order->status);
    }

    public function test_koki_can_complete_order(): void
    {
        $order = Order::first();

        $response = $this->actingAs($this->koki)->patchJson("/kds/orders/{$order->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }

    public function test_koki_can_update_individual_item_status(): void
    {
        $item = OrderItem::first();

        $response = $this->actingAs($this->koki)->patchJson("/kds/items/{$item->id}/status", [
            'status' => 'cooking',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals('cooking', $item->status);
    }

    public function test_koki_can_cancel_item_with_reason(): void
    {
        $item = OrderItem::first();

        $response = $this->actingAs($this->koki)->postJson("/kds/items/{$item->id}/cancel", [
            'reason' => 'Bahan baku wagyu mendadak habis',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals('cancelled', $item->status);
        $this->assertEquals('Bahan baku wagyu mendadak habis', $item->cancel_reason);
    }

    public function test_koki_can_toggle_menu_sold_out(): void
    {
        $menu = Menu::first();
        $initialAvailable = $menu->is_available;

        $response = $this->actingAs($this->koki)->postJson("/kds/menus/{$menu->id}/sold-out");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $menu->refresh();
        $this->assertNotEquals($initialAvailable, $menu->is_available);
    }
}
