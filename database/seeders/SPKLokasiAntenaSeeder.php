<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKLokasiAntenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data lokasi antena (untuk SPK Survey yang memerlukan)
     */
    public function run(): void
    {
        $lokasi_antena = [
            // ========================================
            // LOKASI ANTENA SPK 6: SURVEY MLT
            // Untuk instalasi Fiber Optic/Wireless
            // ========================================
            [
                'id_lokasi_antena' => 1,
                'id_spk' => 6,
                'lokasi_antena' => 'Atap dak betok',
                'detail_lokasi_antena' => null,
                'space_tersedia' => 'x meter',
                'akses_di_lokasi_perlu_alat_bantu' => 'Tidak',
                'penangkal_petir' => 'tidak_ada',
                'tinggi_penangkal_petir' => null,
                'jarak_ke_lokasi_antena' => null,
                'tindak_lanjut' => null,
                'tower_pole' => 'tidak_ada',
                'pemilik_tower_pole' => null,
            ],
        ];

        DB::table('SPK_Lokasi_Antena')->insert($lokasi_antena);
    }
}