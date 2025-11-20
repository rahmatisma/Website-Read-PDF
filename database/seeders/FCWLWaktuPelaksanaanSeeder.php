<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLWaktuPelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data waktu pelaksanaan Form Checklist Wireless (8 jenis waktu)
     */
    public function run(): void
    {
        $waktu = [
            // ========================================
            // WAKTU PELAKSANAAN FCWL SPK 4
            // Timeline maintenance wireless BNI
            // ========================================
            [
                'id_waktu' => 1,
                'id_fcwl' => 1,
                'jenis_waktu' => 'perintah',
                'waktu' => Carbon::parse('2021-06-21 18:00:00'),
            ],
            [
                'id_waktu' => 2,
                'id_fcwl' => 1,
                'jenis_waktu' => 'persiapan',
                'waktu' => Carbon::parse('2021-06-21 18:08:00'),
            ],
            [
                'id_waktu' => 3,
                'id_fcwl' => 1,
                'jenis_waktu' => 'berangkat',
                'waktu' => Carbon::parse('2021-06-21 18:08:00'),
            ],
            [
                'id_waktu' => 4,
                'id_fcwl' => 1,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => Carbon::parse('2021-06-21 19:42:00'),
            ],
            [
                'id_waktu' => 5,
                'id_fcwl' => 1,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => Carbon::parse('2021-06-21 19:42:00'),
            ],
            [
                'id_waktu' => 6,
                'id_fcwl' => 1,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            // Note: Jam Pulang dan Tiba Kantor tidak diisi (NULL)
        ];

        DB::table('FCWL_Waktu_Pelaksanaan')->insert($waktu);
    }
}