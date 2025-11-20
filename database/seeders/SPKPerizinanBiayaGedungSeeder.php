<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPerizinanBiayaGedungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_perizinan_biaya_gedung')->insert([
            [
                'id_perizinan_gedung' => 1,
                'id_spk' => 4, // SPK Survey
                'pic_bm' => 'Budi Santoso',
                'kontak_pic_bm' => '08123456789',
                'material_dan_infrastruktur' => 'Lintas Arta',
                'panjang_kabel_dalam_gedung' => '150 meter',
                'pelaksana_penarikan_kabel_dalam_gedung' => 'Vendor Lintasarta',
                'waktu_pelaksanaan_penarikan_kabel' => '2023-11-20 s/d 2023-11-22',
                'supervisi' => 'Team Lintasarta',
                'deposit_kerja' => 'Rp 5.000.000',
                'ikg_instalasi_kabel_gedung' => 'Rp 3.000.000',
                'biaya_sewa' => 'Rp 2.000.000/bulan',
                'biaya_lain' => 'Rp 500.000',
                'info_lain_lain_jika_ada' => 'Pembayaran dilakukan setelah instalasi selesai',
            ],
        ]);
    }
}