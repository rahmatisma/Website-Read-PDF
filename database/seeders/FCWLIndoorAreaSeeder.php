<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLIndoorAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data indoor area untuk Form Checklist Wireless
     * Berisi info UPS, ruangan, suhu, grounding, dll
     */
    public function run(): void
    {
        $indoor = [
            // ========================================
            // INDOOR AREA FCWL SPK 4
            // Data sarana penunjang indoor
            // ========================================
            [
                'id_indoor' => 1,
                'id_fcwl' => 1,
                'merk_ups' => 'Ya',
                'kapasitas_ups' => 'Sinus, Continu',
                'jenis_ups' => null,
                'ruangan_bebas_debu' => 'ya',
                'suhu_ruangan' => 26.0,
                'terpasang_ground_bar' => 'ya',
                'catuan_input_modem' => 'UPS',
                'v_input_modem_p_n' => 210.0,
                'v_input_modem_n_g' => 1.0,
                'bertumpuk' => 'tidak',
                'lokasi_ruang' => null,
                'suhu_casing_modem' => null,
                'catuan_input_terbounding' => 'ya',
                'splicing_konektor_kabel' => 'Baik, rapi',
                'pemilik_perangkat_cpe' => 'Pelanggan',
                'jenis_perangkat_cpe' => null,
            ],
        ];

        DB::table('FCWL_Indoor_Area')->insert($indoor);
    }
}