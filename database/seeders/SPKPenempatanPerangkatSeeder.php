<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPenempatanPerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data penempatan perangkat modem dan router (untuk SPK Survey)
     */
    public function run(): void
    {
        $penempatan = [
            // ========================================
            // PENEMPATAN PERANGKAT SPK 6: SURVEY MLT
            // ========================================
            [
                'id_penempatan' => 1,
                'id_spk' => 6,
                'lokasi_penempatan_modem_dan_router' => null,
                'kesiapan_ruang_server' => 'siap',
                'ketersedian_rak_server' => 'ada',
                'space_modem_dan_router' => 'ada',
                'diizinkan_foto_ruang_server_pelanggan' => 'ya',
            ],
        ];

        DB::table('SPK_Penempatan_Perangkat')->insert($penempatan);
    }
}