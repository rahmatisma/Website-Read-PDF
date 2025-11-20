<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKSarpenTeganganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data tegangan listrik untuk ruang server (PLN, UPS, IT, Generator)
     * Note: Data survey MLT tidak memiliki detail tegangan, akan diisi sample data
     */
    public function run(): void
    {
        $tegangan = [
            // ========================================
            // TEGANGAN SARPEN SPK 6: SURVEY MLT
            // Sample data tegangan standar Indonesia
            // ========================================
            
            // PLN
            [
                'id_tegangan' => 1,
                'id_sarpen' => 1,
                'jenis_sumber' => 'pln',
                'p_n' => 220.50,
                'p_g' => 220.30,
                'n_g' => 0.20,
            ],
            
            // UPS
            [
                'id_tegangan' => 2,
                'id_sarpen' => 1,
                'jenis_sumber' => 'ups',
                'p_n' => 220.00,
                'p_g' => 219.80,
                'n_g' => 0.20,
            ],
            
            // IT (biasanya tidak ada di survey sederhana)
            [
                'id_tegangan' => 3,
                'id_sarpen' => 1,
                'jenis_sumber' => 'it',
                'p_n' => null,
                'p_g' => null,
                'n_g' => null,
            ],
            
            // Generator (biasanya tidak ada di survey sederhana)
            [
                'id_tegangan' => 4,
                'id_sarpen' => 1,
                'jenis_sumber' => 'generator',
                'p_n' => null,
                'p_g' => null,
                'n_g' => null,
            ],
        ];

        DB::table('SPK_Sarpen_Tegangan')->insert($tegangan);
    }
}