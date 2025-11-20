<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLPerangkatAntenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data perangkat antenna untuk Form Checklist Wireless
     */
    public function run(): void
    {
        $antenna = [
            // ========================================
            // PERANGKAT ANTENNA FCWL SPK 4
            // Data antenna dan instalasinya
            // ========================================
            [
                'id_antena' => 1,
                'id_fcwl' => 1,
                'polarisasi' => '. MDPL',
                'altitude' => null,
                'lokasi' => null,
                'antena_terbounding_dengan_ground' => 'ya',
                'posisi_antena_sejajar' => 'ya',
            ],
        ];

        DB::table('FCWL_Perangkat_Antena')->insert($antenna);
    }
}