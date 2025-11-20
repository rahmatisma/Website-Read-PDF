<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLOutdoorAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data outdoor area untuk Form Checklist Wireless
     * Berisi info BS, LOS, mounting, penangkal petir, dll
     */
    public function run(): void
    {
        $outdoor = [
            // ========================================
            // OUTDOOR AREA FCWL SPK 4
            // Data kondisi outdoor dan mounting
            // ========================================
            [
                'id_outdoor' => 1,
                'id_fcwl' => 1,
                'bs_catuan_sektor' => 'Ya',
                'los_ke_bs_catuan' => 'ya',
                'jarak_udara' => null,
                'heading' => null,
                'latitude' => null,
                'longitude' => null,
                'potential_obstacle' => null,
                'type_mounting' => 'Others',
                'mounting_tidak_goyang' => 'ya',
                'center_of_gravity' => 'Canester = 0 (Tegak lurus)',
                'disekitar_mounting_ada_penangkal_petir' => 'ya',
                'sudut_mounting_terhadap_penangkal_petir' => '<45',
                'tinggi_mounting' => 'Meter',
                'type_penangkal_petir' => 'N/A',
            ],
        ];

        DB::table('FCWL_Outdoor_Area')->insert($outdoor);
    }
}