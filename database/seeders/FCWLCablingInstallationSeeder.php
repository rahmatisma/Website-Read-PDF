<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLCablingInstallationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabling = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'type_kabel_ifl' => 'RG6 Coaxial',
                'panjang_kabel_ifl' => '35 meter',
                'tahanan_short_kabel_ifl' => 'Open Circuit (OK)',
                'terpasang_arrestor' => 'Ya, kencang',
                'splicing_konektor_kabel_ifl' => 'Rapat, Baik',
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'type_kabel_ifl' => 'RG11 Coaxial',
                'panjang_kabel_ifl' => '42 meter',
                'tahanan_short_kabel_ifl' => 'Open Circuit (OK)',
                'terpasang_arrestor' => 'Ya, kencang',
                'splicing_konektor_kabel_ifl' => 'Rapat, Baik',
            ],
        ];

        DB::table('fcwl_cabling_installation')->insert($cabling);
        
        $this->command->info('✓ FCWL_Cabling_Installation seeded: 2 records');
    }
}