<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWWaktuPelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fcw_waktu_pelaksanaan')->insert([
            // Form Checklist Wireline #1 (SPK Aktivasi)
            [
                'id_waktu' => 1,
                'id_fcw' => 1,
                'jenis_waktu' => 'perintah',
                'waktu' => '2021-05-20 09:21:00',
            ],
            [
                'id_waktu' => 2,
                'id_fcw' => 1,
                'jenis_waktu' => 'persiapan',
                'waktu' => '2021-05-20 10:23:00',
            ],
            [
                'id_waktu' => 3,
                'id_fcw' => 1,
                'jenis_waktu' => 'berangkat',
                'waktu' => '2021-05-20 10:23:00',
            ],
            [
                'id_waktu' => 4,
                'id_fcw' => 1,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => '2021-05-20 10:45:00',
            ],
            [
                'id_waktu' => 5,
                'id_fcw' => 1,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => '2021-05-20 10:50:00',
            ],
            [
                'id_waktu' => 6,
                'id_fcw' => 1,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => '2021-05-20 12:20:00',
            ],
            [
                'id_waktu' => 7,
                'id_fcw' => 1,
                'jenis_waktu' => 'pulang',
                'waktu' => '2021-05-20 12:30:00',
            ],
            [
                'id_waktu' => 8,
                'id_fcw' => 1,
                'jenis_waktu' => 'tiba_kantor',
                'waktu' => '2021-05-20 13:45:00',
            ],
            
            // Form Checklist Wireline #2 (SPK Survey)
            [
                'id_waktu' => 9,
                'id_fcw' => 2,
                'jenis_waktu' => 'perintah',
                'waktu' => '2023-11-09 08:00:00',
            ],
            [
                'id_waktu' => 10,
                'id_fcw' => 2,
                'jenis_waktu' => 'persiapan',
                'waktu' => '2023-11-09 09:00:00',
            ],
            [
                'id_waktu' => 11,
                'id_fcw' => 2,
                'jenis_waktu' => 'berangkat',
                'waktu' => '2023-11-09 09:30:00',
            ],
            [
                'id_waktu' => 12,
                'id_fcw' => 2,
                'jenis_waktu' => 'tiba_lokasi',
                'waktu' => '2023-11-09 11:00:00',
            ],
            [
                'id_waktu' => 13,
                'id_fcw' => 2,
                'jenis_waktu' => 'mulai_kerja',
                'waktu' => '2023-11-09 11:15:00',
            ],
            [
                'id_waktu' => 14,
                'id_fcw' => 2,
                'jenis_waktu' => 'selesai_kerja',
                'waktu' => '2023-11-09 14:30:00',
            ],
            [
                'id_waktu' => 15,
                'id_fcw' => 2,
                'jenis_waktu' => 'pulang',
                'waktu' => '2023-11-09 14:45:00',
            ],
            [
                'id_waktu' => 16,
                'id_fcw' => 2,
                'jenis_waktu' => 'tiba_kantor',
                'waktu' => '2023-11-09 16:30:00',
            ],
        ]);
    }
}