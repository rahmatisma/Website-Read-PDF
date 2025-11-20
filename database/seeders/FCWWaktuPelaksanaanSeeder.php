<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWWaktuPelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data waktu pelaksanaan Form Checklist Wireline (8 jenis waktu)
     */
    public function run(): void
    {
        $waktu = [
            // ========================================
            // WAKTU PELAKSANAAN FCW SPK 3
            // Timeline maintenance wireline BNI
            // ========================================
            [
                'id_waktu' => 1,
                'id_fcw' => 1,
                'jenis_waktu' => 'perintah',
                'waktu' => Carbon::parse('2021-05-20 09:21:00'),
            ],
            [
                'id_waktu' => 2,
                'id_fcw' => 1,
                'jenis_waktu' => 'persiapan',
                'waktu' => Carbon::parse('2021-05-20 10:23:00'),
            ],
            [
                'id_waktu' => 3,
                'id_fcw' => 1,
                'jenis_waktu' => 'berangkat',
                'waktu' => Carbon::parse('2021-05-20 10:23:00'),
            ],
            [
                'id_waktu' => 4,
                'id_fcw' => 1,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => Carbon::parse('2021-05-20 10:45:00'),
            ],
            [
                'id_waktu' => 5,
                'id_fcw' => 1,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => Carbon::parse('2021-05-20 10:50:00'),
            ],
            [
                'id_waktu' => 6,
                'id_fcw' => 1,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            // Note: Jam Pulang dan Tiba Kantor tidak diisi (NULL)
        ];

        DB::table('FCW_Waktu_Pelaksanaan')->insert($waktu);
    }
}