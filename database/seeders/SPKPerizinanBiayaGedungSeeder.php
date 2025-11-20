<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPerizinanBiayaGedungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data perizinan dan biaya gedung (untuk SPK Survey)
     */
    public function run(): void
    {
        $perizinan_gedung = [
            // ========================================
            // PERIZINAN GEDUNG SPK 6: SURVEY MLT
            // ========================================
            [
                'id_perizinan_gedung' => 1,
                'id_spk' => 6,
                'pic_bm' => null,
                'kontak_pic_bm' => null,
                'material_dan_infrastruktur' => 'Lintas Arta',
                'panjang_kabel_dalam_gedung' => null,
                'pelaksana_penarikan_kabel_dalam_gedung' => null,
                'waktu_pelaksanaan_penarikan_kabel' => null,
                'supervisi' => null,
                'deposit_kerja' => null,
                'ikg_instalasi_kabel_gedung' => null,
                'biaya_sewa' => null,
                'biaya_lain' => null,
                'info_lain_lain_jika_ada' => null,
            ],
        ];

        DB::table('SPK_Perizinan_Biaya_Gedung')->insert($perizinan_gedung);
    }
}
