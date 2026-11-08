<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Welcome back!');
        $response->assertSee('Amarta Boga');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/pos');
        $response->assertRedirect('/login');

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect('/login');

        $responseKds = $this->get('/kds');
        $responseKds->assertRedirect('/login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::where('email', 'kasir@amartaboga.com')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/pos');
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@amartaboga.com')->first();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_koki_is_redirected_to_kds(): void
    {
        $koki = User::where('email', 'koki@amartaboga.com')->first();

        $response = $this->post('/login', [
            'email' => $koki->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/kds');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::where('email', 'kasir@amartaboga.com')->first();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::where('email', 'kasir@amartaboga.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
