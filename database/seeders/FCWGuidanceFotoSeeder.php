<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWGuidanceFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $guidanceFoto = [
            // FCW #1 - BNI Aktivasi - MAINTENANCE PHOTOS
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Teknisi Yang Aktivasi',
                'patch_foto' => 'guidance/fcw1_teknisi_aktivasi.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Kondisi Sebelum Perbaikan',
                'patch_foto' => 'guidance/fcw1_kondisi_sebelum.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Action / Perbaikan',
                'patch_foto' => 'guidance/fcw1_action_perbaikan.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Kondisi Setelah Perbaikan',
                'patch_foto' => 'guidance/fcw1_kondisi_setelah.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Test Ping',
                'patch_foto' => 'guidance/fcw1_test_ping.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Catuan Listrik',
                'patch_foto' => 'guidance/fcw1_catuan_listrik.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Indikator Perangkat',
                'patch_foto' => 'guidance/fcw1_indikator_perangkat.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'jenis_foto' => 'Kondisi Rak/Meja Penempatan Perangkat',
                'patch_foto' => 'guidance/fcw1_rak_perangkat.jpg',
                'created_at' => $now,
            ],
            
            // FCW #2 - Multimedia Survey - SURVEY PHOTOS
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Teknisi Survey',
                'patch_foto' => 'guidance/fcw2_teknisi_survey.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Ruang Server / Penempatan Perangkat',
                'patch_foto' => 'guidance/fcw2_ruang_server.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Jalur Kabel Dalam Gedung',
                'patch_foto' => 'guidance/fcw2_jalur_kabel.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Rak Server Available',
                'patch_foto' => 'guidance/fcw2_rak_available.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Power Outlet',
                'patch_foto' => 'guidance/fcw2_power_outlet.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'jenis_foto' => 'Grounding Bar',
                'patch_foto' => 'guidance/fcw2_grounding_bar.jpg',
                'created_at' => $now,
            ],
        ];

        DB::table('fcw_guidance_foto')->insert($guidanceFoto);
        
        $this->command->info('✓ FCW_Guidance_Foto seeded: 14 records (8 for FCW #1, 6 for FCW #2)');
    }
}