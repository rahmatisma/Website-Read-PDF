<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWTeganganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data tegangan untuk Form Checklist Wireline
     * Hanya PLN yang terisi, UPS/IT/Generator kosong
     */
    public function run(): void
    {
        $tegangan = [
            // ========================================
            // TEGANGAN FCW SPK 3: PLN
            // Data dari form: P-N: 225.8, P-G: 225.5, N-G: 0.054
            // ========================================
            [
                'id_tegangan' => 1,
                'id_fcw' => 1,
                'jenis_sumber' => 'pln',
                'p_n' => 225.8,
                'p_g' => 225.5,
                'n_g' => 0.054,
            ],
            
            // ========================================
            // TEGANGAN FCW SPK 3: UPS (kosong)
            // ========================================
            [
                'id_tegangan' => 2,
                'id_fcw' => 1,
                'jenis_sumber' => 'ups',
                'p_n' => null,
                'p_g' => null,
                'n_g' => null,
            ],
            
            // ========================================
            // TEGANGAN FCW SPK 3: IT (kosong)
            // ========================================
            [
                'id_tegangan' => 3,
                'id_fcw' => 1,
                'jenis_sumber' => 'it',
                'p_n' => null,
                'p_g' => null,
                'n_g' => null,
            ],
            
            // ========================================
            // TEGANGAN FCW SPK 3: Generator (kosong)
            // ========================================
            [
                'id_tegangan' => 4,
                'id_fcw' => 1,
                'jenis_sumber' => 'generator',
                'p_n' => null,
                'p_g' => null,
                'n_g' => null,
            ],
        ];

        DB::table('FCW_Tegangan')->insert($tegangan);
    }
}