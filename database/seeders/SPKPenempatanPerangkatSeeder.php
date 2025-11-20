<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPenempatanPerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_penempatan_perangkat')->insert([
            [
                'id_penempatan' => 1,
                'id_spk' => 4, // SPK Survey
                'lokasi_penempatan_modem_dan_router' => 'Ruang Server Lantai 2',
                'kesiapan_ruang_server' => 'Siap',
                'ketersedian_rak_server' => 'Ada',
                'space_modem_dan_router' => 'Ada',
                'diizinkan_foto_ruang_server_pelanggan' => 'Ya',
            ],
        ]);
    }
}