<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data List Item perangkat yang dicabut/dipasang
     * Hanya untuk SPK Dismantle yang memiliki data item
     */
    public function run(): void
    {
        $list_item = [
            // ========================================
            // LIST ITEM SPK 5: DISMANTLE BNI
            // 3 item perangkat yang dicabut
            // ========================================
            [
                'id_item' => 1,
                'id_spk' => 5,
                'kode' => 'B2WN0262020HA1346',
                'deskripsi' => 'FORTIGATE-50E HW AND WARRANTY',
                'serial_number' => null,
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
            [
                'id_item' => 2,
                'id_spk' => 5,
                'kode' => 'B2WN0286208MA1443',
                'deskripsi' => 'PSU FORTI FG-50E/FG-30E',
                'serial_number' => null,
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
            [
                'id_item' => 3,
                'id_spk' => 5,
                'kode' => 'B2WN0150152MA0732',
                'deskripsi' => 'HUAAWEI Router B311As + Antena',
                'serial_number' => null,
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
        ];

        DB::table('List_Item')->insert($list_item);
    }
}