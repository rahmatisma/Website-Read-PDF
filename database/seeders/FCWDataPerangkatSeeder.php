<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWDataPerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $perangkat = [
            // FCW #1 - BNI Aktivasi - EXISTING DEVICES
            [
                'id_fcw' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'HUAWEI Router B311As + Antena',
                'no_reg' => 'B2WN0150152MA0732',
                'serial_number' => 'SN-HW-B311-2021-001',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'FORTIGATE-50E HW AND WARRANTY',
                'no_reg' => 'B2WN0262020HA1346',
                'serial_number' => 'FGT50E-2021-001346',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'PSU FORTI FG-50E/FG-30E',
                'no_reg' => 'B2WN0286208MA1443',
                'serial_number' => 'PSU-FGT-2021-1443',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'ADAPTOR 12V 1A',
                'no_reg' => 'B2WN0150153MA0732',
                'serial_number' => 'ADP-12V-2021-0732',
                'created_at' => $now,
            ],
            
            // FCW #2 - Multimedia Survey - TIDAK ADA PERANGKAT (karena masih survey)
            // Kosongkan untuk survey, nanti diisi saat instalasi
        ];

        DB::table('fcw_data_perangkat')->insert($perangkat);
        
        $this->command->info('✓ FCW_Data_Perangkat seeded: 4 records (FCW #1 only, FCW #2 empty for survey)');
    }
}