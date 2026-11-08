<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Main Course', 'slug' => 'main-course', 'icon' => 'utensils', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Favorit', 'slug' => 'favorit', 'icon' => 'star', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Food', 'slug' => 'food', 'icon' => 'bowl-food', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Drink', 'slug' => 'drink', 'icon' => 'wine-glass', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Coffee', 'slug' => 'coffee', 'icon' => 'coffee', 'sort_order' => 5, 'is_active' => true],
            ['name' => 'Dessert', 'slug' => 'dessert', 'icon' => 'cake-candles', 'sort_order' => 6, 'is_active' => true],
            ['name' => 'Salad', 'slug' => 'salad', 'icon' => 'seedling', 'sort_order' => 7, 'is_active' => true],
            ['name' => 'Soup', 'slug' => 'soup', 'icon' => 'mug-hot', 'sort_order' => 8, 'is_active' => true],
            ['name' => 'Ice Cream', 'slug' => 'ice-cream', 'icon' => 'ice-cream', 'sort_order' => 9, 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
