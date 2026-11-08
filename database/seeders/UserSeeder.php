<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@amartaboga.com'],
            [
                'name' => 'Budi Santoso (Admin)',
                'password' => Hash::make('password'),
                'phone' => '081234567801',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // Kasir / Staff
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@amartaboga.com'],
            [
                'name' => 'Siti Nurhaliza (Kasir)',
                'password' => Hash::make('password'),
                'phone' => '081234567802',
                'is_active' => true,
            ]
        );
        $kasir->syncRoles(['staff']);

        // Koki / Kitchen
        $koki = User::firstOrCreate(
            ['email' => 'koki@amartaboga.com'],
            [
                'name' => 'Chef Kenji Pratama (Koki)',
                'password' => Hash::make('password'),
                'phone' => '081234567803',
                'is_active' => true,
            ]
        );
        $koki->syncRoles(['koki']);
    }
}
