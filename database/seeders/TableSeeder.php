<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            // Lantai 1 Indoor
            ['number' => '01', 'name' => 'Meja 01', 'capacity' => 2, 'location' => 'Lantai 1 Indoor', 'status' => 'available'],
            ['number' => '02', 'name' => 'Meja 02', 'capacity' => 2, 'location' => 'Lantai 1 Indoor', 'status' => 'available'],
            ['number' => '03', 'name' => 'Meja 03', 'capacity' => 4, 'location' => 'Lantai 1 Indoor', 'status' => 'occupied'], // Occupied for test
            ['number' => '04', 'name' => 'Meja 04', 'capacity' => 4, 'location' => 'Lantai 1 Indoor', 'status' => 'available'],
            ['number' => '05', 'name' => 'Meja 05', 'capacity' => 6, 'location' => 'Lantai 1 Indoor', 'status' => 'available'],
            ['number' => '06', 'name' => 'Meja 06', 'capacity' => 6, 'location' => 'Lantai 1 Indoor', 'status' => 'available'],

            // Lantai 2 Indoor
            ['number' => '07', 'name' => 'Meja 07', 'capacity' => 4, 'location' => 'Lantai 2 Indoor', 'status' => 'available'],
            ['number' => '08', 'name' => 'Meja 08', 'capacity' => 4, 'location' => 'Lantai 2 Indoor', 'status' => 'available'],
            ['number' => '09', 'name' => 'Meja 09', 'capacity' => 6, 'location' => 'Lantai 2 Indoor', 'status' => 'available'],
            ['number' => '10', 'name' => 'Meja 10', 'capacity' => 8, 'location' => 'Lantai 2 Indoor', 'status' => 'reserved'],

            // Outdoor
            ['number' => 'OUT-1', 'name' => 'Meja Outdoor 01', 'capacity' => 4, 'location' => 'Area Outdoor', 'status' => 'available'],
            ['number' => 'OUT-2', 'name' => 'Meja Outdoor 02', 'capacity' => 4, 'location' => 'Area Outdoor', 'status' => 'available'],
            ['number' => 'OUT-3', 'name' => 'Meja Outdoor 03', 'capacity' => 4, 'location' => 'Area Outdoor', 'status' => 'available'],
            ['number' => 'OUT-4', 'name' => 'Meja Outdoor 04', 'capacity' => 6, 'location' => 'Area Outdoor', 'status' => 'cleaning'],

            // VIP Room
            ['number' => 'VIP-1', 'name' => 'VIP Room Garuda', 'capacity' => 10, 'location' => 'Ruang VIP', 'status' => 'available'],
            ['number' => 'VIP-2', 'name' => 'VIP Room Sakura', 'capacity' => 12, 'location' => 'Ruang VIP', 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::firstOrCreate(['number' => $table['number']], $table);
        }
    }
}
