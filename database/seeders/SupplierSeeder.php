<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Sumber Daging Nusantara',
                'contact_person' => 'Hendra Setiawan',
                'phone' => '0811-9876-543',
                'email' => 'sales@sumberdaging.co.id',
                'address' => 'Kawasan Industri Pulo Gadung Blok C No. 12, Jakarta Timur',
                'is_active' => true,
            ],
            [
                'name' => 'CV Sayur Segar Lembang',
                'contact_person' => 'Asep Kurnia',
                'phone' => '0813-8822-1144',
                'email' => 'order@lembangfresh.com',
                'address' => 'Jl. Raya Lembang No. 45, Bandung Barat',
                'is_active' => true,
            ],
            [
                'name' => 'PT Sukanda Djaya Dairy & Frozen',
                'contact_person' => 'Melisa Chandra',
                'phone' => '0812-4455-6677',
                'email' => 'cs@sukandadjaya.com',
                'address' => 'Jl. Pasir Kaliki No. 102, Jakarta Barat',
                'is_active' => true,
            ],
            [
                'name' => 'PT Indoprima Kemasan & Struk',
                'contact_person' => 'Dwi Cahyo',
                'phone' => '0857-3322-1100',
                'email' => 'sales@indoprimapack.id',
                'address' => 'Jl. Daan Mogot Km 14, Jakarta Barat',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
