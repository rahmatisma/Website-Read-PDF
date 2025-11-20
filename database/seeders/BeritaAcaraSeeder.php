<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeritaAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Berita Acara untuk setiap SPK
     */
    public function run(): void
    {
        $berita_acara = [
            // ========================================
            // BERITA ACARA SPK 1: INSTALASI BNI
            // ========================================
            [
                'id_berita_acara' => 1,
                'id_spk' => 1,
                'judul_spk' => 'BERITA ACARA',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
                'updated_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],

            // ========================================
            // BERITA ACARA SPK 2: AKTIVASI BNI
            // ========================================
            [
                'id_berita_acara' => 2,
                'id_spk' => 2,
                'judul_spk' => 'BERITA ACARA',
                'created_at' => Carbon::parse('2021-03-04 09:55:00'),
                'updated_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],

            // ========================================
            // BERITA ACARA SPK 5: DISMANTLE BNI
            // ========================================
            [
                'id_berita_acara' => 3,
                'id_spk' => 5,
                'judul_spk' => 'BERITA ACARA',
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
                'updated_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],

            // ========================================
            // BERITA ACARA SPK 6: SURVEY MLT
            // ========================================
            [
                'id_berita_acara' => 4,
                'id_spk' => 6,
                'judul_spk' => 'BERITA ACARA',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
                'updated_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
        ];

        DB::table('Berita_Acara')->insert($berita_acara);
    }
}