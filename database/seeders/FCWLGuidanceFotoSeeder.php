<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLGuidanceFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $guidanceFoto = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Teknisi di Lokasi',
                'patch_foto' => 'guidance/fcwl1_teknisi_lokasi.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Kondisi Antenna Sebelum Maintenance',
                'patch_foto' => 'guidance/fcwl1_antenna_before.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Proses Maintenance Antenna',
                'patch_foto' => 'guidance/fcwl1_maintenance_process.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Kondisi Antenna Setelah Maintenance',
                'patch_foto' => 'guidance/fcwl1_antenna_after.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Indoor Unit & Modem',
                'patch_foto' => 'guidance/fcwl1_indoor_unit.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Signal Strength Test',
                'patch_foto' => 'guidance/fcwl1_signal_test.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Grounding & Arrestor',
                'patch_foto' => 'guidance/fcwl1_grounding.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'jenis_foto' => 'Kabel IFL Installation',
                'patch_foto' => 'guidance/fcwl1_kabel_ifl.jpg',
                'created_at' => $now,
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Tim Instalasi',
                'patch_foto' => 'guidance/fcwl2_tim_instalasi.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Pemasangan Antenna di Rooftop',
                'patch_foto' => 'guidance/fcwl2_antenna_install.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Mounting Bracket',
                'patch_foto' => 'guidance/fcwl2_mounting.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Aiming & Alignment Antenna',
                'patch_foto' => 'guidance/fcwl2_aiming.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Indoor Equipment Installation',
                'patch_foto' => 'guidance/fcwl2_indoor_equipment.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Kabel Routing Indoor',
                'patch_foto' => 'guidance/fcwl2_kabel_routing.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Testing & Commissioning',
                'patch_foto' => 'guidance/fcwl2_testing.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'jenis_foto' => 'Final Configuration',
                'patch_foto' => 'guidance/fcwl2_final_config.jpg',
                'created_at' => $now,
            ],
        ];

        DB::table('fcwl_guidance_foto')->insert($guidanceFoto);
        
        $this->command->info('✓ FCWL_Guidance_Foto seeded: 16 records (8 per FCWL)');
    }
}