<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            TableSeeder::class,
            SupplierSeeder::class,
            IngredientSeeder::class,
            CategorySeeder::class,
            // MenuSeeder::class, // Dinonaktifkan: Menu diinput mandiri lewat panel Admin
            DiscountSeeder::class,
            // SampleOrderSeeder::class,
        ]);
    }
}
