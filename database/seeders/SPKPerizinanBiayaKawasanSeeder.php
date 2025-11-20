<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPerizinanBiayaKawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_perizinan_biaya_kawasan')->insert([
            [
                'id_perizinan_kawasan' => 1,
                'id_spk' => 4, // SPK Survey
                'melewati_kawasan_private' => 'Ya',
                'nama_kawasan' => 'Griya Alam Sentosa Residence',
                'pic_kawasan' => 'Pak Rahmat',
                'kontak_pic_kawasan' => '08567890123',
                'panjang_kabel_dalam_kawasan' => '200 meter',
                'pelaksana_penarikan_kabel_dalam_kawasan' => 'Vendor Lintasarta',
                'deposit_kerja' => 'Rp 3.000.000',
                'supervisi' => 'Team Kawasan',
                'biaya_penarikan_kabel_dalam_kawasan' => 'Rp 5.000.000',
                'biaya_sewa' => 'Rp 1.000.000/bulan',
                'biaya_lain' => 'Rp 300.000',
                'info_lain_lain_jika_ada' => 'Perlu koordinasi dengan pengelola kawasan',
            ],
        ]);
    }
}