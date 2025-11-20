<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SPKPelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data waktu pelaksanaan untuk setiap SPK
     */
    public function run(): void
    {
        $pelaksanaan = [
            // ========================================
            // PELAKSANAAN SPK 1: INSTALASI BNI
            // Durasi: 18 menit (15:22 - 15:40)
            // ========================================
            [
                'id_pelaksanaan' => 1,
                'id_spk' => 1,
                'permintaan_pelanggan' => Carbon::parse('2021-03-03 15:00:00'),
                'datang' => Carbon::parse('2021-03-03 15:22:00'),
                'selesai' => Carbon::parse('2021-03-03 15:40:00'),
            ],

            // ========================================
            // PELAKSANAAN SPK 2: AKTIVASI BNI
            // Durasi: ~17 jam (16:38 - 09:55 next day)
            // ========================================
            [
                'id_pelaksanaan' => 2,
                'id_spk' => 2,
                'permintaan_pelanggan' => Carbon::parse('2021-03-03 16:38:00'),
                'datang' => Carbon::parse('2021-03-03 16:46:00'),
                'selesai' => Carbon::parse('2021-03-04 09:55:00'),
            ],

            // ========================================
            // PELAKSANAAN SPK 3: FCW BNI
            // Durasi: 1 jam 35 menit (10:45 - 12:20)
            // ========================================
            [
                'id_pelaksanaan' => 3,
                'id_spk' => 3,
                'permintaan_pelanggan' => Carbon::parse('2021-05-20 09:21:00'),
                'datang' => Carbon::parse('2021-05-20 10:45:00'),
                'selesai' => Carbon::parse('2021-05-20 12:20:00'),
            ],

            // ========================================
            // PELAKSANAAN SPK 4: FCWL BNI
            // Durasi: 4 jam 21 menit (19:42 - 00:03 next day)
            // ========================================
            [
                'id_pelaksanaan' => 4,
                'id_spk' => 4,
                'permintaan_pelanggan' => Carbon::parse('2021-06-21 18:00:00'),
                'datang' => Carbon::parse('2021-06-21 19:42:00'),
                'selesai' => Carbon::parse('2021-06-22 00:03:00'),
            ],

            // ========================================
            // PELAKSANAAN SPK 5: DISMANTLE BNI
            // Durasi: ~12 jam (15:05 - 03:16 next day)
            // ========================================
            [
                'id_pelaksanaan' => 5,
                'id_spk' => 5,
                'permintaan_pelanggan' => Carbon::parse('2025-09-11 15:00:00'),
                'datang' => Carbon::parse('2025-09-11 15:05:00'),
                'selesai' => Carbon::parse('2025-09-12 03:16:00'),
            ],

            // ========================================
            // PELAKSANAAN SPK 6: SURVEY MLT
            // Durasi: ~2 hari (17:13 - 10:02, 2 hari kemudian)
            // ========================================
            [
                'id_pelaksanaan' => 6,
                'id_spk' => 6,
                'permintaan_pelanggan' => Carbon::parse('2023-11-09 11:00:00'),
                'datang' => Carbon::parse('2023-11-11 17:13:00'),
                'selesai' => Carbon::parse('2023-11-13 10:02:00'),
            ],
        ];

        DB::table('SPK_Pelaksanaan')->insert($pelaksanaan);
    }
}

