<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKHHBaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Handhole Baru yang direncanakan (untuk SPK Survey)
     * Data ini diisi dengan sample realistis karena data asli NULL semua
     */
    public function run(): void
    {
        $hh_baru = [
            // ========================================
            // HH BARU 1 - SPK 6: SURVEY MLT
            // Lokasi: Belakang gedung, area parkir
            // ========================================
            [
                'id_hh_baru' => 1,
                'id_spk' => 6,
                'nomor_hh' => 1,
                'lokasi_hh' => 'Belakang gedung, area parkir',
                'latitude' => -6.39535,
                'longitude' => 106.88380,
                'kebutuhan_penambahan_closure' => 'Perlu 1 unit closure baru',
                'kapasitas_closure' => '1:8',
            ],

            // ========================================
            // HH BARU 2 - SPK 6: SURVEY MLT
            // Lokasi: Sisi kanan gedung, dekat gerbang masuk
            // ========================================
            [
                'id_hh_baru' => 2,
                'id_spk' => 6,
                'nomor_hh' => 2,
                'lokasi_hh' => 'Sisi kanan gedung, dekat gerbang masuk',
                'latitude' => -6.39552,
                'longitude' => 106.88358,
                'kebutuhan_penambahan_closure' => 'Perlu 1 unit closure baru',
                'kapasitas_closure' => '1:4',
            ],
        ];

        DB::table('SPK_HH_Baru')->insert($hh_baru);
    }
}