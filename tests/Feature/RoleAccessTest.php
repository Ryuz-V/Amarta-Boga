<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::where('email', 'kasir@amartaboga.com')->first();

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        // Spatie middleware throws 403 unauthorized for wrong role
        $response->assertStatus(403);
    }

    public function test_koki_cannot_access_admin_dashboard(): void
    {
        $koki = User::where('email', 'koki@amartaboga.com')->first();

        $response = $this->actingAs($koki)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_all_areas(): void
    {
        $admin = User::where('email', 'admin@amartaboga.com')->first();

        $responseAdmin = $this->actingAs($admin)->get('/admin/dashboard');
        $responseAdmin->assertStatus(200);

        $responsePos = $this->actingAs($admin)->get('/pos');
        $responsePos->assertStatus(200);

        $responseKds = $this->actingAs($admin)->get('/kds');
        $responseKds->assertStatus(200);
    }
}
