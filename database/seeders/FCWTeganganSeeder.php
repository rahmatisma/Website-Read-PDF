<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWTeganganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fcw_tegangan')->insert([
            // Form Checklist Wireline #1 (SPK Aktivasi)
            [
                'id_tegangan' => 1,
                'id_fcw' => 1,
                'jenis_sumber' => 'pln',
                'p_n' => '225.8',
                'p_g' => '225.5',
                'n_g' => '0.054',
            ],
            [
                'id_tegangan' => 2,
                'id_fcw' => 1,
                'jenis_sumber' => 'ups',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
            [
                'id_tegangan' => 3,
                'id_fcw' => 1,
                'jenis_sumber' => 'it',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
            
            // Form Checklist Wireline #2 (SPK Survey)
            [
                'id_tegangan' => 4,
                'id_fcw' => 2,
                'jenis_sumber' => 'pln',
                'p_n' => '223.5',
                'p_g' => '223.2',
                'n_g' => '0.1',
            ],
            [
                'id_tegangan' => 5,
                'id_fcw' => 2,
                'jenis_sumber' => 'ups',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
            [
                'id_tegangan' => 6,
                'id_fcw' => 2,
                'jenis_sumber' => 'it',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
        ]);
    }
}