<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKHHBaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_hh_baru')->insert([
            [
                'id_hh_baru' => 1,
                'id_spk' => 4, // SPK Survey
                'nomor_hh' => 1,
                'lokasi_hh' => 'Sebelah Pos Satpam Griya Alam Sentosa',
                'longitude_dan_latitude_hh' => '-6.39548, 106.88378',
                'kebutuhan_penambahan_closure' => 'Ya',
                'kapasitas_closure' => '24 Core',
            ],
            [
                'id_hh_baru' => 2,
                'id_spk' => 4, // SPK Survey
                'nomor_hh' => 2,
                'lokasi_hh' => 'Depan Gedung Pelanggan',
                'longitude_dan_latitude_hh' => '-6.39542, 106.88372',
                'kebutuhan_penambahan_closure' => 'Ya',
                'kapasitas_closure' => '12 Core',
            ],
        ]);
    }
}