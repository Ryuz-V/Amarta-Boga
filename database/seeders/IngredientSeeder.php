<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $supplierMeat = Supplier::where('name', 'like', '%Daging%')->first();
        $supplierVeg = Supplier::where('name', 'like', '%Sayur%')->first();
        $supplierDairy = Supplier::where('name', 'like', '%Dairy%')->first();

        $ingredients = [
            // Sapi & Unggas
            [
                'name' => 'Daging Sapi Wagyu Slice MB5',
                'sku' => 'ING-WAGYU-01',
                'unit' => 'gram',
                'current_stock' => 15000, // 15 kg
                'minimum_stock' => 3000,  // Alert threshold 3 kg
                'cost_per_unit' => 350,   // Rp 350 / gram = Rp 350.000 / kg
                'supplier_id' => $supplierMeat?->id,
            ],
            [
                'name' => 'Buntut Sapi Pilihan',
                'sku' => 'ING-BUNTUT-01',
                'unit' => 'gram',
                'current_stock' => 12000,
                'minimum_stock' => 2500,
                'cost_per_unit' => 180,
                'supplier_id' => $supplierMeat?->id,
            ],
            [
                'name' => 'Fillet Dada Ayam',
                'sku' => 'ING-AYAM-01',
                'unit' => 'gram',
                'current_stock' => 25000,
                'minimum_stock' => 5000,
                'cost_per_unit' => 65, // Rp 65.000 / kg
                'supplier_id' => $supplierMeat?->id,
            ],
            [
                'name' => 'Bebek Utuh Karkas',
                'sku' => 'ING-BEBEK-01',
                'unit' => 'pcs',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'cost_per_unit' => 55000,
                'supplier_id' => $supplierMeat?->id,
            ],
            [
                'name' => 'Telur Ayam Negeri',
                'sku' => 'ING-TELUR-01',
                'unit' => 'pcs',
                'current_stock' => 250,
                'minimum_stock' => 50,
                'cost_per_unit' => 2200,
                'supplier_id' => $supplierDairy?->id,
            ],

            // Bahan Pokok & Sayur
            [
                'name' => 'Beras Pandan Wangi',
                'sku' => 'ING-BERAS-01',
                'unit' => 'gram',
                'current_stock' => 50000, // 50 kg
                'minimum_stock' => 10000,
                'cost_per_unit' => 16, // Rp 16.000 / kg
                'supplier_id' => $supplierVeg?->id,
            ],
            [
                'name' => 'Bawang Putih & Bawang Merah Kupas',
                'sku' => 'ING-BAWANG-01',
                'unit' => 'gram',
                'current_stock' => 8000,
                'minimum_stock' => 1500,
                'cost_per_unit' => 40,
                'supplier_id' => $supplierVeg?->id,
            ],
            [
                'name' => 'Cabe Rawit Merah Segar',
                'sku' => 'ING-CABE-01',
                'unit' => 'gram',
                'current_stock' => 5000,
                'minimum_stock' => 1000,
                'cost_per_unit' => 70,
                'supplier_id' => $supplierVeg?->id,
            ],
            [
                'name' => 'Minyak Goreng Sawit',
                'sku' => 'ING-MINYAK-01',
                'unit' => 'ml',
                'current_stock' => 40000, // 40 liter
                'minimum_stock' => 8000,
                'cost_per_unit' => 18,
                'supplier_id' => $supplierDairy?->id,
            ],

            // Dairy & Minuman
            [
                'name' => 'Keju Mozzarella Shredded',
                'sku' => 'ING-MOZZA-01',
                'unit' => 'gram',
                'current_stock' => 6000,
                'minimum_stock' => 1500,
                'cost_per_unit' => 130,
                'supplier_id' => $supplierDairy?->id,
            ],
            [
                'name' => 'Biji Kopi Arabika Gayo Specialty',
                'sku' => 'ING-KOPI-01',
                'unit' => 'gram',
                'current_stock' => 10000,
                'minimum_stock' => 2000,
                'cost_per_unit' => 250,
                'supplier_id' => $supplierDairy?->id,
            ],
            [
                'name' => 'Susu UHT Fresh Milk',
                'sku' => 'ING-SUSU-01',
                'unit' => 'ml',
                'current_stock' => 30000, // 30 liter
                'minimum_stock' => 5000,
                'cost_per_unit' => 20,
                'supplier_id' => $supplierDairy?->id,
            ],
            [
                'name' => 'Bubuk Uji Matcha Premium',
                'sku' => 'ING-MATCHA-01',
                'unit' => 'gram',
                'current_stock' => 2500,
                'minimum_stock' => 500,
                'cost_per_unit' => 600,
                'supplier_id' => $supplierDairy?->id,
            ],
            [
                'name' => 'Sirup Gula Aren Organik',
                'sku' => 'ING-AREN-01',
                'unit' => 'ml',
                'current_stock' => 15000,
                'minimum_stock' => 3000,
                'cost_per_unit' => 35,
                'supplier_id' => $supplierDairy?->id,
            ],
            [
                'name' => 'Mie Ramen Basah Segar',
                'sku' => 'ING-RAMEN-01',
                'unit' => 'porsi',
                'current_stock' => 80,
                'minimum_stock' => 20,
                'cost_per_unit' => 5000,
                'supplier_id' => $supplierDairy?->id,
            ],
        ];

        foreach ($ingredients as $item) {
            $supplierId = $item['supplier_id'];
            unset($item['supplier_id']);

            $ingredient = Ingredient::firstOrCreate(['sku' => $item['sku']], $item);

            // Record initial stock-in movement
            StockMovement::firstOrCreate(
                [
                    'ingredient_id' => $ingredient->id,
                    'type' => 'in',
                    'reference_type' => 'InitialStock',
                ],
                [
                    'supplier_id' => $supplierId,
                    'user_id' => $admin?->id,
                    'quantity' => $ingredient->current_stock,
                    'before_stock' => 0,
                    'after_stock' => $ingredient->current_stock,
                    'cost_per_unit' => $ingredient->cost_per_unit,
                    'notes' => 'Stok awal pembukaan sistem POS',
                ]
            );
        }
    }
}
