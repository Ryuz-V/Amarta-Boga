<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@amartaboga.com')->first();
        $this->staff = User::where('email', 'kasir@amartaboga.com')->first();
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }

    public function test_admin_can_create_new_user(): void
    {
        $unique = uniqid();
        $email = "barista_{$unique}@amartaboga.com";

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Barista Rian',
            'email' => $email,
            'phone' => '081299887766',
            'role' => 'staff',
            'password' => 'secret123',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $targetUser = User::where('email', 'kasir@amartaboga.com')->first();
        $initialStatus = $targetUser->is_active;

        $response = $this->actingAs($this->admin)->patch("/admin/users/{$targetUser->id}/toggle");
        $response->assertStatus(302);

        $targetUser->refresh();
        $this->assertNotEquals($initialStatus, $targetUser->is_active);

        // Toggle back to active for other tests
        $targetUser->is_active = true;
        $targetUser->save();
    }

    public function test_admin_can_create_category(): void
    {
        $unique = uniqid();
        $name = "Snack Dimsum {$unique}";

        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => $name,
            'icon' => 'utensils',
            'sort_order' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['name' => $name]);
    }

    public function test_admin_can_create_and_toggle_menu_availability(): void
    {
        $category = Category::first();
        $unique = uniqid();
        $menuName = "Test Burger Wagyu {$unique}";

        $response = $this->actingAs($this->admin)->post('/admin/menus', [
            'category_id' => $category->id,
            'name' => $menuName,
            'price' => 75000,
            'cost_price' => 30000,
            'description' => 'Burger daging wagyu empuk dengan saus spesial',
            'is_active' => '1',
            'is_available' => '1',
        ]);

        $response->assertRedirect('/admin/menus');
        $menu = Menu::where('name', $menuName)->first();
        $this->assertNotNull($menu);

        // Toggle Sold Out
        $toggleResponse = $this->actingAs($this->admin)->patch("/admin/menus/{$menu->id}/toggle");
        $toggleResponse->assertStatus(302);

        $menu->refresh();
        $this->assertFalse($menu->is_available);
    }

    public function test_admin_can_adjust_ingredient_stock(): void
    {
        $ingredient = Ingredient::first();
        $initialStock = (float) $ingredient->current_stock;
        $addQuantity = 50.0;

        $response = $this->actingAs($this->admin)->post('/admin/inventory/adjust', [
            'ingredient_id' => $ingredient->id,
            'type' => 'in',
            'quantity' => $addQuantity,
            'notes' => 'Pembelian stok tambahan untuk tes',
        ]);

        $response->assertRedirect('/admin/inventory');
        $ingredient->refresh();
        $this->assertEquals($initialStock + $addQuantity, (float) $ingredient->current_stock);
    }

    public function test_admin_can_create_table(): void
    {
        $unique = rand(100, 999);
        $number = "T-{$unique}";

        $response = $this->actingAs($this->admin)->post('/admin/tables', [
            'number' => $number,
            'name' => "Meja Tes {$unique}",
            'capacity' => 6,
            'location' => 'Lantai 1 Indoor',
            'status' => 'available',
        ]);

        $response->assertRedirect('/admin/tables');
        $this->assertDatabaseHas('tables', ['number' => $number]);
    }

    public function test_admin_can_create_discount(): void
    {
        $unique = rand(100, 999);
        $code = "DISC{$unique}";

        $response = $this->actingAs($this->admin)->post('/admin/discounts', [
            'name' => "Diskon Weekend {$unique}%",
            'code' => $code,
            'type' => 'percentage',
            'value' => 15.0,
            'min_spend' => 100000,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/discounts');
        $this->assertDatabaseHas('discounts', ['code' => $code]);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'settings' => [
                'restaurant_name' => 'Amarta Boga Resto Flagship',
                'tax_percentage' => '11',
            ],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('settings', [
            'key' => 'restaurant_name',
            'value' => 'Amarta Boga Resto Flagship',
        ]);
    }

    public function test_staff_cannot_access_any_admin_crud(): void
    {
        $this->actingAs($this->staff)->get('/admin/users')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/menus')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/inventory')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/tables')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/discounts')->assertStatus(403);
        $this->actingAs($this->staff)->get('/admin/settings')->assertStatus(403);
    }
}
