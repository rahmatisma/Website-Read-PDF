<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKHHEksistingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Handhole Eksisting (untuk SPK Survey)
     * Data ini diisi dengan sample realistis karena data asli NULL semua
     */
    public function run(): void
    {
        $hh_eksisting = [
            // ========================================
            // HH EKSISTING 1 - SPK 6: SURVEY MLT
            // Lokasi: Depan gedung utama
            // ========================================
            [
                'id_hh_eksisting' => 1,
                'id_spk' => 6,
                'nomor_hh' => 1,
                'kondisi_hh' => 'Baik',
                'lokasi_hh' => 'Depan gedung utama, pinggir jalan',
                'latitude' => -6.39541,
                'longitude' => 106.88371,
                'ketersediaan_closure' => 'Ada',
                'kapasitas_closure' => '1:8',
                'kondisi_closure' => 'Baik, tidak berkarat',
            ],

            // ========================================
            // HH EKSISTING 2 - SPK 6: SURVEY MLT
            // Lokasi: Samping gedung, dekat tiang listrik
            // ========================================
            [
                'id_hh_eksisting' => 2,
                'id_spk' => 6,
                'nomor_hh' => 2,
                'kondisi_hh' => 'Cukup Baik',
                'lokasi_hh' => 'Samping gedung, dekat tiang listrik',
                'latitude' => -6.39548,
                'longitude' => 106.88365,
                'ketersediaan_closure' => 'Ada',
                'kapasitas_closure' => '1:4',
                'kondisi_closure' => 'Ada sedikit korosi, perlu maintenance',
            ],
        ];

        DB::table('SPK_HH_Eksisting')->insert($hh_eksisting);
    }
}