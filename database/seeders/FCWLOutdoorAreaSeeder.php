<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLOutdoorAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outdoorArea = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'bs_catuan_sektor' => 'BTS Kalimalang',
                'los_ke_bs_catuan' => 'Ya',
                'jarak_udara' => '2.3 km',
                'heading' => '285°',
                'latitude' => '-6.24854',
                'longitude' => '106.98937',
                'potential_obstacle' => 'Tidak ada obstacle signifikan, LOS clear',
                'type_mounting' => 'Wall Mount',
                'mounting_tidak_goyang' => 'Ya',
                'center_of_gravity' => 'Center of gravity Canester = 0 (Tegak lurus)',
                'disekitar_mounting_ada_penangkal_petir' => 'Ya',
                'sudut_mounting_terhadap_penangkal_petir' => '35°',
                'tinggi_mounting' => '12 meter',
                'type_penangkal_petir' => 'Franklin Rod',
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'bs_catuan_sektor' => 'BTS Bekasi Timur',
                'los_ke_bs_catuan' => 'Ya',
                'jarak_udara' => '3.8 km',
                'heading' => '310°',
                'latitude' => '-6.39541',
                'longitude' => '106.88371',
                'potential_obstacle' => 'Ada pohon tinggi di area perumahan, masih dalam toleransi Fresnel Zone',
                'type_mounting' => 'Pole Mount',
                'mounting_tidak_goyang' => 'Ya',
                'center_of_gravity' => 'Center of gravity Canester = 0 (Tegak lurus)',
                'disekitar_mounting_ada_penangkal_petir' => 'Ya',
                'sudut_mounting_terhadap_penangkal_petir' => '40°',
                'tinggi_mounting' => '15 meter',
                'type_penangkal_petir' => 'ESE (Early Streamer Emission)',
            ],
        ];

        DB::table('fcwl_outdoor_area')->insert($outdoorArea);
        
        $this->command->info('✓ FCWL_Outdoor_Area seeded: 2 records');
    }
}