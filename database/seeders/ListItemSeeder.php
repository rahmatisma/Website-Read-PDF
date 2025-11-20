<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('list_item')->insert([
            // SPK Dismantle - Items yang dicabut
            [
                'id_item' => 1,
                'id_spk' => 2,
                'kode' => 'B2WN0262020HA1346',
                'deskripsi' => 'FORTIGATE-50E HW AND WARRANTY',
                'created_at' => Carbon::now(),
            ],
            [
                'id_item' => 2,
                'id_spk' => 2,
                'kode' => 'B2WN0286208MA1443',
                'deskripsi' => 'PSU FORTI FG-50E/FG-30E',
                'created_at' => Carbon::now(),
            ],
            [
                'id_item' => 3,
                'id_spk' => 2,
                'kode' => 'B2WN0150152MA0732',
                'deskripsi' => 'HUAAWEI Router B311As + Antena',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}