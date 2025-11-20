<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLGuidanceFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data guidance foto untuk Form Checklist Wireless (10 jenis)
     * 6 foto terisi, 4 kosong (placeholder)
     */
    public function run(): void
    {
        $guidance = [
            // ========================================
            // GUIDANCE FOTO FCWL SPK 4 (10 jenis)
            // Halaman 4: 6 foto terisi
            // ========================================
            [
                'id_guidance' => 1,
                'id_fcwl' => 1,
                'jenis_foto' => 'teknisi_aktivasi',
                'path_foto' => 'fcwl_guidance/teknisi_aktivasi_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 2,
                'id_fcwl' => 1,
                'jenis_foto' => 'kondisi_sebelum_perbaikan',
                'path_foto' => 'fcwl_guidance/kondisi_sebelum_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 3,
                'id_fcwl' => 1,
                'jenis_foto' => 'action_perbaikan',
                'path_foto' => 'fcwl_guidance/action_perbaikan_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 4,
                'id_fcwl' => 1,
                'jenis_foto' => 'kondisi_setelah_perbaikan',
                'path_foto' => 'fcwl_guidance/kondisi_setelah_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 5,
                'id_fcwl' => 1,
                'jenis_foto' => 'test_ping',
                'path_foto' => 'fcwl_guidance/test_ping_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 6,
                'id_fcwl' => 1,
                'jenis_foto' => 'catuan_listrik',
                'path_foto' => 'fcwl_guidance/catuan_listrik_hal4.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],

            // ========================================
            // Halaman 5: 2 foto terisi
            // ========================================
            [
                'id_guidance' => 7,
                'id_fcwl' => 1,
                'jenis_foto' => 'indikator_perangkat',
                'path_foto' => 'fcwl_guidance/indikator_perangkat_hal5.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 8,
                'id_fcwl' => 1,
                'jenis_foto' => 'kondisi_rak_penempatan',
                'path_foto' => 'fcwl_guidance/kondisi_rak_hal5.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],

            // ========================================
            // Foto khusus wireless (jika ada)
            // ========================================
            [
                'id_guidance' => 9,
                'id_fcwl' => 1,
                'jenis_foto' => 'antenna_installation',
                'path_foto' => 'fcwl_guidance/antenna_installation.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_guidance' => 10,
                'id_fcwl' => 1,
                'jenis_foto' => 'outdoor_mounting',
                'path_foto' => 'fcwl_guidance/outdoor_mounting.jpg',
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
        ];

        DB::table('FCWL_Guidance_Foto')->insert($guidance);
    }
}