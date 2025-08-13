<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Item_Pekerjaan')->insert([
            [
                'id_item' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'id_perangkat' => 1, // relasi ke tabel Perangkat
                'kategori' => 'Instalasi',
                'catatan' => 'Pemasangan perangkat di rak server dengan konfigurasi awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_item' => 2,
                'id_spk' => 1,
                'id_perangkat' => 2,
                'kategori' => 'Maintenance',
                'catatan' => 'Pembersihan dan pengecekan kabel koneksi',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
