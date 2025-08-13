<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor')->insert([
            [
                'nama_vendor' => 'KOPKARLA',
                'nama_teknisi' => 'Ruslan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_vendor' => 'PT Mitra Solusi',
                'nama_teknisi' => 'Rina Kurnia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
