<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKInformasiGedungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data informasi gedung (hanya untuk SPK yang memerlukan)
     */
    public function run(): void
    {
        $informasi_gedung = [
            // ========================================
            // INFORMASI GEDUNG SPK 2: AKTIVASI BNI
            // ========================================
            [
                'id_info_gedung' => 1,
                'id_spk' => 2,
                'alamat' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'status_gedung' => null,
                'kondisi_gedung' => null,
                'pemilik_bangunan' => null,
                'kontak_person' => 'TUQINO',
                'bagian_jabatan' => null,
                'telpon_fax' => '0215728563',
                'email' => null,
                'jumlah_lantai_gedung' => null,
                'pelanggan_fo' => null,
                'penempatan_antena' => null,
                'sewa_space_antena' => null,
                'sewa_shaft_kabel' => null,
                'biaya_ikg' => null,
                'penanggungjawab_sewa' => null,
            ],

            // ========================================
            // INFORMASI GEDUNG SPK 5: DISMANTLE BNI
            // ========================================
            [
                'id_info_gedung' => 2,
                'id_spk' => 5,
                'alamat' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'status_gedung' => null,
                'kondisi_gedung' => null,
                'pemilik_bangunan' => null,
                'kontak_person' => 'TUQINO',
                'bagian_jabatan' => null,
                'telpon_fax' => '0215728563',
                'email' => null,
                'jumlah_lantai_gedung' => null,
                'pelanggan_fo' => null,
                'penempatan_antena' => null,
                'sewa_space_antena' => null,
                'sewa_shaft_kabel' => null,
                'biaya_ikg' => null,
                'penanggungjawab_sewa' => null,
            ],

            // ========================================
            // INFORMASI GEDUNG SPK 6: SURVEY MLT
            // Data lengkap dari survey
            // ========================================
            [
                'id_info_gedung' => 3,
                'id_spk' => 6,
                'alamat' => 'JL. GRIYA ALAM SENTOSA,',
                'status_gedung' => 'Milik Sendiri',
                'kondisi_gedung' => 'Sudah Siap',
                'pemilik_bangunan' => null,
                'kontak_person' => null,
                'bagian_jabatan' => null,
                'telpon_fax' => null,
                'email' => null,
                'jumlah_lantai_gedung' => null,
                'pelanggan_fo' => 'FO',
                'penempatan_antena' => 'Tidak Perlu Izin',
                'sewa_space_antena' => 'Ada',
                'sewa_shaft_kabel' => 'Ada',
                'biaya_ikg' => 'Ada',
                'penanggungjawab_sewa' => 'Lintasarta',
            ],
        ];

        DB::table('SPK_Informasi_Gedung')->insert($informasi_gedung);
    }
}

