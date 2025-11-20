<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKHHEksistingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_hh_eksisting')->insert([
            [
                'id_hh_eksisting' => 1,
                'id_spk' => 4, // SPK Survey
                'nomor_hh' => 1,
                'kondisi_hh' => 'Baik',
                'lokasi_hh' => 'Depan Gerbang Griya Alam Sentosa',
                'longitude_dan_latitude_hh' => '-6.39541, 106.88371',
                'ketersediaan_closure' => 'Ada',
                'kapasitas_closure' => '24 Core',
                'kondisi_closure' => 'Baik',
            ],
            [
                'id_hh_eksisting' => 2,
                'id_spk' => 4, // SPK Survey
                'nomor_hh' => 2,
                'kondisi_hh' => 'Rusak',
                'lokasi_hh' => 'Pertigaan Jalan Griya Alam',
                'longitude_dan_latitude_hh' => '-6.39555, 106.88385',
                'ketersediaan_closure' => 'Ada',
                'kapasitas_closure' => '12 Core',
                'kondisi_closure' => 'Perlu Penggantian',
            ],
        ]);
    }
}