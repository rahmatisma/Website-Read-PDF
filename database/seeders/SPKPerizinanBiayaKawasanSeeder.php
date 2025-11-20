<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPerizinanBiayaKawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data perizinan dan biaya kawasan private (untuk SPK Survey)
     */
    public function run(): void
    {
        $perizinan_kawasan = [
            // ========================================
            // PERIZINAN KAWASAN SPK 6: SURVEY MLT
            // Melewati kawasan private: Ya
            // ========================================
            [
                'id_perizinan_kawasan' => 1,
                'id_spk' => 6,
                'melewati_kawasan_private' => 'ya',
                'nama_kawasan' => null,
                'pic_kawasan' => null,
                'kontak_pic_kawasan' => null,
                'panjang_kabel_dalam_kawasan' => null,
                'pelaksana_penarikan_kabel_dalam_kawasan' => null,
                'deposit_kerja' => null,
                'supervisi' => null,
                'biaya_penarikan_kabel_dalam_kawasan' => null,
                'biaya_sewa' => null,
                'biaya_lain' => null,
                'info_lain_lain_jika_ada' => null,
            ],
        ];

        DB::table('SPK_Perizinan_Biaya_Kawasan')->insert($perizinan_kawasan);
    }
}