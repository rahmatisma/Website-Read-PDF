<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLPerangkatAntenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perangkatAntenna = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'polarisasi' => 'Vertical',
                'altitude' => '45 MDPL',
                'lokasi' => 'Rooftop Gedung BNI ATM Center',
                'antenna_terbounding_dengan_ground' => 'Ya, kencang',
                'posisi_antena_sejajar' => 'Ya',
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'polarisasi' => 'Horizontal',
                'altitude' => '38 MDPL',
                'lokasi' => 'Rooftop Gedung Kantor Cabang',
                'antenna_terbounding_dengan_ground' => 'Ya, kencang',
                'posisi_antena_sejajar' => 'Ya',
            ],
        ];

        DB::table('fcwl_perangkat_antenna')->insert($perangkatAntenna);
        
        $this->command->info('✓ FCWL_Perangkat_Antenna seeded: 2 records');
    }
}