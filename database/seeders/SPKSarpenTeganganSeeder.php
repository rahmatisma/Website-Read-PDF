<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKSarpenTeganganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_sarpen_tegangan')->insert([
            [
                'id_tegangan' => 1,
                'id_sarpen' => 1, // SPK Survey Sarpen
                'jenis_sumber' => 'pln',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
            [
                'id_tegangan' => 2,
                'id_sarpen' => 1, // SPK Survey Sarpen
                'jenis_sumber' => 'ups',
                'p_n' => '220',
                'p_g' => '220',
                'n_g' => '0',
            ],
        ]);
    }
}