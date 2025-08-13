<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Data_Lokasi')->insert([
            [
                'id_lokasi' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'latitude' => -6.200000, // contoh koordinat
                'longitude' => 106.816666, // contoh koordinat
                'posisi_modem' => 'Dilantai',
                'ruang' => 'Atm Center',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
