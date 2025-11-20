<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKKawasanUmumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data kawasan umum yang dilewati (untuk SPK Survey)
     */
    public function run(): void
    {
        $kawasan_umum = [
            // ========================================
            // KAWASAN UMUM SPK 6: SURVEY MLT
            // Data kosong tapi struktur ada
            // ========================================
            [
                'id_kawasan_umum' => 1,
                'id_spk' => 6,
                'nama_kawasan_umum_pu_yang_dilewati' => null,
                'panjang_jalur_outdoor_di_kawasan_umum' => null,
            ],
        ];

        DB::table('SPK_Kawasan_Umum')->insert($kawasan_umum);
    }
}