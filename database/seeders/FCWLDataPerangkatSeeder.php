<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLDataPerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data perangkat untuk Form Checklist Wireless
     * 4 EXISTING devices (sama dengan yang di dismantle)
     */
    public function run(): void
    {
        $perangkat = [
            // ========================================
            // EXISTING DEVICES (4 items)
            // ========================================
            [
                'id_perangkat' => 1,
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'PSU FORTI FG-50E/FG-30E',
                'no_reg' => 'B2WN0286208MA1443',
                'serial_number' => null,
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_perangkat' => 2,
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'FORTIGATE-50E HW AND WARRANTY',
                'no_reg' => 'B2WN0262020HA1346',
                'serial_number' => null,
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_perangkat' => 3,
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'ADAPTOR 12V 1A',
                'no_reg' => 'B2WN0150153MA0732',
                'serial_number' => null,
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
            [
                'id_perangkat' => 4,
                'id_fcwl' => 1,
                'kategori' => 'existing',
                'nama_barang' => 'HUAAWEI Router B311As + Antena',
                'no_reg' => 'B2WN0150152MA0732',
                'serial_number' => null,
                'created_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],

            // Note: TIDAK TERPAKAI, CABUT, dan PENGGANTI/PASANG BARU kosong
        ];

        DB::table('FCWL_Data_Perangkat')->insert($perangkat);
    }
}