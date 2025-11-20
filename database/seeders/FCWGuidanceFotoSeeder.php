<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWGuidanceFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data guidance foto untuk Form Checklist Wireline (8 jenis)
     */
    public function run(): void
    {
        $guidance = [
            // ========================================
            // GUIDANCE FOTO FCW SPK 3 (8 jenis)
            // ========================================
            [
                'id_guidance' => 1,
                'id_fcw' => 1,
                'jenis_foto' => 'teknisi_aktivasi',
                'path_foto' => 'fcw_guidance/teknisi_aktivasi_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 2,
                'id_fcw' => 1,
                'jenis_foto' => 'kondisi_sebelum_perbaikan',
                'path_foto' => 'fcw_guidance/kondisi_sebelum_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 3,
                'id_fcw' => 1,
                'jenis_foto' => 'action_perbaikan',
                'path_foto' => 'fcw_guidance/action_perbaikan_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 4,
                'id_fcw' => 1,
                'jenis_foto' => 'kondisi_setelah_perbaikan',
                'path_foto' => 'fcw_guidance/kondisi_setelah_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 5,
                'id_fcw' => 1,
                'jenis_foto' => 'test_ping',
                'path_foto' => 'fcw_guidance/test_ping_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 6,
                'id_fcw' => 1,
                'jenis_foto' => 'catuan_listrik',
                'path_foto' => 'fcw_guidance/catuan_listrik_hal4.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 7,
                'id_fcw' => 1,
                'jenis_foto' => 'indikator_perangkat',
                'path_foto' => 'fcw_guidance/indikator_perangkat_hal5.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_guidance' => 8,
                'id_fcw' => 1,
                'jenis_foto' => 'kondisi_rak_penempatan',
                'path_foto' => 'fcw_guidance/kondisi_rak_hal5.jpg',
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
        ];

        DB::table('FCW_Guidance_Foto')->insert($guidance);
    }
}