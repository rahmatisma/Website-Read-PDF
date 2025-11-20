<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKLokasiAntenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_lokasi_antena')->insert([
            [
                'id_lokasi_antena' => 1,
                'id_spk' => 4, // SPK Survey
                'lokasi_antena' => 'Atap dak betok',
                'detail_lokasi_antena' => 'Rooftop gedung lantai 3',
                'space_tersedia' => 'x meter',
                'akses_di_lokasi_perlu_alat_bantu' => 'Tidak',
                'penangkal_petir' => 'Tidak',
                'tinggi_penangkal_petir' => null,
                'jarak_ke_lokasi_antena' => null,
                'tindak_lanjut' => 'Perlu instalasi penangkal petir',
                'tower_pole' => 'Tidak',
                'pemilik_tower_pole' => null,
            ],
        ]);
    }
}