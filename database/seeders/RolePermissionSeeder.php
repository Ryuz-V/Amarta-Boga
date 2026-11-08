<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions list
        $permissions = [
            // Admin permissions
            'manage-users',
            'manage-categories',
            'manage-menus',
            'manage-inventory',
            'manage-discounts',
            'manage-tables',
            'manage-settings',
            'view-reports',
            'export-reports',
            'approve-void',

            // Kasir / Staff permissions
            'view-pos',
            'create-orders',
            'process-payments',
            'hold-orders',
            'print-receipts',
            'view-active-orders',
            'open-shift',
            'close-shift',

            // Koki / Kitchen permissions
            'view-kds',
            'update-kitchen-order-status',
            'toggle-menu-sold-out',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $kokiRole  = Role::firstOrCreate(['name' => 'koki']);

        // Assign permissions
        $adminRole->syncPermissions(Permission::all());

        $staffRole->syncPermissions([
            'view-pos',
            'create-orders',
            'process-payments',
            'hold-orders',
            'print-receipts',
            'view-active-orders',
            'open-shift',
            'close-shift',
        ]);

        $kokiRole->syncPermissions([
            'view-kds',
            'update-kitchen-order-status',
            'toggle-menu-sold-out',
        ]);
    }
}
