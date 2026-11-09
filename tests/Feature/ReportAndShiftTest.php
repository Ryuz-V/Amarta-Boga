<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportAndShiftTest extends TestCase
{
    use DatabaseTransactions;

    protected User $cashier;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier = User::where('email', 'kasir@amartaboga.com')->first();
        $this->admin = User::where('email', 'admin@amartaboga.com')->first();

        if (Menu::count() === 0) {
            $cat = Category::first() ?? Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
            Menu::create([
                'category_id' => $cat->id,
                'name' => 'Menu Laporan Test',
                'slug' => 'menu-laporan-test',
                'price' => 30000,
                'cost_price' => 12000,
                'is_active' => true,
                'is_available' => true,
            ]);
        }
    }

    public function test_admin_can_access_sales_report_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/sales');

        $response->assertStatus(200);
        $response->assertSee('Laporan & Analitik Penjualan');
        $response->assertSee('Penjualan Bersih');
        $response->assertSee('Laba Kotor Resto');
        $response->assertSee('Tren Penjualan Harian');
    }

    public function test_admin_can_filter_sales_report_by_presets(): void
    {
        $presets = ['today', 'yesterday', '7days', '30days', 'this_month'];

        foreach ($presets as $preset) {
            $response = $this->actingAs($this->admin)->get("/admin/reports/sales?preset={$preset}");
            $response->assertStatus(200);
        }

        // Custom range
        $response = $this->actingAs($this->admin)->get('/admin/reports/sales?preset=custom&start_date=2026-01-01&end_date=2026-12-31');
        $response->assertStatus(200);
    }

    public function test_admin_can_export_sales_report_to_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/sales/excel?start_date=2026-01-01&end_date=2026-12-31');

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', '.xlsx') ||
            str_contains($response->headers->get('content-type') ?? '', 'spreadsheet')
        );
    }

    public function test_admin_can_export_sales_report_to_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/sales/pdf?start_date=2026-01-01&end_date=2026-12-31');

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type') ?? '', 'pdf')
        );
    }

    public function test_cashier_can_check_current_shift_status(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/pos/shift/current');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'has_active_shift',
            'shift'
        ]);
    }

    public function test_cashier_can_open_shift_and_close_with_variance_calculation(): void
    {
        // 1. Pastikan tidak ada shift aktif
        Shift::where('user_id', $this->cashier->id)->where('status', 'open')->update([
            'status' => 'closed',
            'end_time' => now(),
            'actual_cash' => 100000,
            'expected_cash' => 100000,
            'difference' => 0
        ]);

        // 2. Buka shift baru
        $openResponse = $this->actingAs($this->cashier)->postJson('/pos/shift/open', [
            'starting_cash' => 150000,
        ]);

        $openResponse->assertStatus(200);
        $openResponse->assertJson([
            'success' => true,
        ]);

        $shiftId = $openResponse->json('shift.id');
        $this->assertDatabaseHas('shifts', [
            'id' => $shiftId,
            'user_id' => $this->cashier->id,
            'starting_cash' => 150000,
            'status' => 'open',
        ]);

        // 3. Cek shift aktif sekarang
        $currentResponse = $this->actingAs($this->cashier)->getJson('/pos/shift/current');
        $currentResponse->assertStatus(200);
        $this->assertTrue($currentResponse->json('has_active_shift'));
        $this->assertEquals(150000, $currentResponse->json('shift.starting_cash'));

        // 4. Tutup shift kasir dengan kas fisik 160.000 (Surplus Rp 10.000)
        $closeResponse = $this->actingAs($this->cashier)->postJson('/pos/shift/close', [
            'actual_cash' => 160000,
            'notes' => 'Tutup shift uji coba surplus 10rb',
        ]);

        $closeResponse->assertStatus(200);
        $closeResponse->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('shifts', [
            'id' => $shiftId,
            'status' => 'closed',
            'actual_cash' => 160000,
            'difference' => 10000,
        ]);
    }

    public function test_cashier_can_hold_order_and_resume_it(): void
    {
        $menu = Menu::where('is_available', true)->first();
        $table = Table::first();

        // 1. Parkir / Hold Order
        $holdResponse = $this->actingAs($this->cashier)->postJson('/pos/hold', [
            'customer_name' => 'Pak Joko Widodo',
            'table_id' => $table->id,
            'order_type' => 'dine_in',
            'subtotal' => 50000,
            'total_amount' => 55000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => 1,
                    'notes' => 'Pedas sedang'
                ]
            ]
        ]);

        $holdResponse->assertStatus(200);
        $heldOrderId = $holdResponse->json('order.id');

        $this->assertDatabaseHas('orders', [
            'id' => $heldOrderId,
            'is_held' => true,
            'status' => 'pending',
            'customer_name' => 'Pak Joko Widodo',
        ]);

        // 2. Cek list pesanan ditahan
        $listResponse = $this->actingAs($this->cashier)->getJson('/pos/held-orders');
        $listResponse->assertStatus(200);
        $this->assertTrue($listResponse->json('success'));
        $this->assertTrue(collect($listResponse->json('orders'))->contains('id', $heldOrderId));

        // 3. Panggil kembali / Resume
        $resumeResponse = $this->actingAs($this->cashier)->postJson("/pos/orders/{$heldOrderId}/resume");
        $resumeResponse->assertStatus(200);
        $resumeResponse->assertJson([
            'success' => true,
        ]);

        // Pastikan record hold telah dipindahkan dari tabel antrean
        $this->assertDatabaseMissing('orders', [
            'id' => $heldOrderId,
        ]);
    }

    public function test_cashier_can_void_held_order(): void
    {
        $menu = Menu::where('is_available', true)->first();

        $holdResponse = $this->actingAs($this->cashier)->postJson('/pos/hold', [
            'customer_name' => 'Tamu Batal',
            'order_type' => 'take_away',
            'subtotal' => 30000,
            'total_amount' => 30000,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'qty' => 1,
                ]
            ]
        ]);

        $orderId = $holdResponse->json('order.id');

        $voidResponse = $this->actingAs($this->cashier)->postJson("/pos/orders/{$orderId}/void", [
            'cancel_reason' => 'Pelanggan buru-buru pergi',
        ]);

        $voidResponse->assertStatus(200);
        $voidResponse->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'cancelled',
            'cancel_reason' => 'Pelanggan buru-buru pergi',
        ]);
    }

    public function test_admin_can_view_shifts_reconciliation_audit(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/shifts');

        $response->assertStatus(200);
        $response->assertSee('Audit & Rekap Shift Kasir');
        $response->assertSee('Total Modal Awal');
        $response->assertSee('Kas Fisik Disetor');
        $response->assertSee('Total Selisih (Variance)');
    }

    public function test_staff_unauthorized_from_admin_reports(): void
    {
        $response = $this->actingAs($this->cashier)->get('/admin/reports/sales');
        $response->assertStatus(403);

        $shiftResponse = $this->actingAs($this->cashier)->get('/admin/reports/shifts');
        $shiftResponse->assertStatus(403);
    }
}
