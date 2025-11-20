<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLWaktuPelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $waktu = [
            // FCWL #1 - BNI Wireless Maintenance (083806/WO-LA/2021)
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'perintah',
                'waktu' => '2021-06-21 18:00:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'persiapan',
                'waktu' => '2021-06-21 18:08:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'berangkat',
                'waktu' => '2021-06-21 18:08:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => '2021-06-21 19:42:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => '2021-06-21 19:42:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => '2021-06-22 00:03:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'pulang',
                'waktu' => '2021-06-22 00:15:00',
            ],
            [
                'id_fcwl' => 1,
                'jenis_waktu' => 'tiba_kantor',
                'waktu' => '2021-06-22 01:30:00',
            ],
            
            // FCWL #2 - BNI Wireless Installation (065752/WO-LA/2021)
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'perintah',
                'waktu' => '2021-03-03 14:00:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'persiapan',
                'waktu' => '2021-03-03 14:30:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'berangkat',
                'waktu' => '2021-03-03 14:45:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => '2021-03-03 15:30:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => '2021-03-03 15:45:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => '2021-03-03 18:20:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'pulang',
                'waktu' => '2021-03-03 18:30:00',
            ],
            [
                'id_fcwl' => 2,
                'jenis_waktu' => 'tiba_kantor',
                'waktu' => '2021-03-03 19:45:00',
            ],
        ];

        DB::table('fcwl_waktu_pelaksanaan')->insert($waktu);
        
        $this->command->info('✓ FCWL_Waktu_Pelaksanaan seeded: 16 records (8 per FCWL)');
    }
}