<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLCablingInstallationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data cabling installation untuk Form Checklist Wireless
     */
    public function run(): void
    {
        $cabling = [
            // ========================================
            // CABLING INSTALLATION FCWL SPK 4
            // Data kabel IFL dan instalasinya
            // ========================================
            [
                'id_cabling' => 1,
                'id_fcwl' => 1,
                'type_kabel_ifl' => '. Meter',
                'panjang_kabel_ifl' => null,
                'tahanan_short_kabel_ifl' => 'Rapat, Baik',
                'terpasang_arrestor' => 'ya',
                'splicing_konektor_kabel_ifl' => null,
            ],
        ];

        DB::table('FCWL_Cabling_Installation')->insert($cabling);
    }
}