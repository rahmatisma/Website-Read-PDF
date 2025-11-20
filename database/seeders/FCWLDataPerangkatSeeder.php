<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLDataPerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $perangkat = [
            // FCWL #1 - BNI Wireless Maintenance - EXISTING
            [
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'PSU FORTI FG-50E/FG-30E',
                'no_reg' => 'B2WN0286208MA1443',
                'serial_number' => 'PSU-FGT-2021-1443',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'FORTIGATE-50E HW AND WARRANTY',
                'no_reg' => 'B2WN0262020HA1346',
                'serial_number' => 'FGT50E-2021-001346',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'ADAPTOR 12V 1A',
                'no_reg' => 'B2WN0150153MA0732',
                'serial_number' => 'ADP-12V-2021-0732',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'HUAWEI Router B311As + Antena',
                'no_reg' => 'B2WN0150152MA0732',
                'serial_number' => 'SN-HW-B311-2021-001',
                'created_at' => $now,
            ],
            
            // FCWL #2 - BNI Wireless Installation - PASANG BARU
            [
                'id_fcwl' => 2,
                'kategori' => 'pengganti_pasang_baru',
                'nama_barang' => 'Ubiquiti NanoStation M5',
                'no_reg' => 'B2WN0290101MA2021',
                'serial_number' => 'UBNT-NSM5-2021-001',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'kategori' => 'pengganti_pasang_baru',
                'nama_barang' => 'TP-Link TL-R600VPN Router',
                'no_reg' => 'B2WN0290102MA2021',
                'serial_number' => 'TPLNK-R600-2021-002',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'kategori' => 'pengganti_pasang_baru',
                'nama_barang' => 'POE Injector 24V 1A',
                'no_reg' => 'B2WN0290103MA2021',
                'serial_number' => 'POE-24V-2021-003',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'kategori' => 'pengganti_pasang_baru',
                'nama_barang' => 'Outdoor Antenna 5GHz',
                'no_reg' => 'B2WN0290104MA2021',
                'serial_number' => 'ANT-5G-2021-004',
                'created_at' => $now,
            ],
        ];

        DB::table('fcwl_data_perangkat')->insert($perangkat);
        
        $this->command->info('✓ FCWL_Data_Perangkat seeded: 8 records (4 existing, 4 new install)');
    }
}