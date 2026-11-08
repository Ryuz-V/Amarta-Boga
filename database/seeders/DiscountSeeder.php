<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Promo Diskon Hemat 10%',
                'code' => 'HEMAT10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_spend' => 100000,
                'max_discount' => 25000,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(30),
                'usage_limit' => 500,
                'usage_count' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Voucher Member VIP 15%',
                'code' => 'MEMBERVIP',
                'type' => 'percentage',
                'value' => 15.00,
                'min_spend' => 150000,
                'max_discount' => 50000,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(60),
                'usage_limit' => 200,
                'usage_count' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Potongan Langsung Rp 20.000',
                'code' => 'POTONG20K',
                'type' => 'fixed',
                'value' => 20000.00,
                'min_spend' => 120000,
                'max_discount' => null,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(14),
                'usage_limit' => 100,
                'usage_count' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $d) {
            Discount::firstOrCreate(['code' => $d['code']], $d);
        }
    }
}
