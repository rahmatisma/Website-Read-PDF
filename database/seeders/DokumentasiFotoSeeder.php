<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DokumentasiFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data dokumentasi foto untuk semua SPK
     * Total: 31 foto dari 4 SPK (Instalasi, Aktivasi, Dismantle, Survey)
     */
    public function run(): void
    {
        $dokumentasi = [
            // ========================================
            // DOKUMENTASI SPK 1: INSTALASI BNI (7 foto)
            // ========================================
            [
                'id_dokumentasi' => 1,
                'id_spk' => 1,
                'kategori_foto' => 'hasil_instalasi',
                'path_foto' => 'output/images/hasil_instalasi_hal2.jpg',
                'urutan' => 1,
                'keterangan' => 'Hasil instalasi perangkat',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 2,
                'id_spk' => 1,
                'kategori_foto' => 'foto_jalur_kabel',
                'path_foto' => 'output/images/photo_instalasi_ethernet_hal3.jpg',
                'urutan' => 2,
                'keterangan' => 'Photo instalasi ethernet',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 3,
                'id_spk' => 1,
                'kategori_foto' => 'foto_jalur_kabel',
                'path_foto' => 'output/images/photo_instalasi_ethernet_hal4_1.jpg',
                'urutan' => 3,
                'keterangan' => 'Photo instalasi ethernet',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 4,
                'id_spk' => 1,
                'kategori_foto' => 'foto_jalur_kabel',
                'path_foto' => 'output/images/photo_instalasi_ethernet_hal4_2.jpg',
                'urutan' => 4,
                'keterangan' => 'Photo instalasi ethernet (duplicate)',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 5,
                'id_spk' => 1,
                'kategori_foto' => 'foto_lain_lain',
                'path_foto' => 'output/images/foto_lain-lain_hal6.jpg',
                'urutan' => 5,
                'keterangan' => 'Foto lain-lain',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 6,
                'id_spk' => 1,
                'kategori_foto' => 'list_item',
                'path_foto' => 'output/images/list_item_hal8.jpg',
                'urutan' => 6,
                'keterangan' => 'List item halaman 8',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],
            [
                'id_dokumentasi' => 7,
                'id_spk' => 1,
                'kategori_foto' => 'list_item',
                'path_foto' => 'output/images/list_item_hal9.jpg',
                'urutan' => 7,
                'keterangan' => 'List item halaman 9',
                'created_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],

            // ========================================
            // DOKUMENTASI SPK 2: AKTIVASI BNI (4 foto)
            // ========================================
            [
                'id_dokumentasi' => 8,
                'id_spk' => 2,
                'kategori_foto' => 'hasil_aktivasi',
                'path_foto' => 'output/images/hasil_aktivasi_hal2.jpg',
                'urutan' => 1,
                'keterangan' => 'Hasil aktivasi layanan',
                'created_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],
            [
                'id_dokumentasi' => 9,
                'id_spk' => 2,
                'kategori_foto' => 'foto_dokumentasi_umum',
                'path_foto' => 'output/images/dokumentasi_foto_hal3.jpg',
                'urutan' => 2,
                'keterangan' => 'Dokumentasi foto halaman 3',
                'created_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],
            [
                'id_dokumentasi' => 10,
                'id_spk' => 2,
                'kategori_foto' => 'foto_dokumentasi_umum',
                'path_foto' => 'output/images/dokumentasi_foto_hal4.jpg',
                'urutan' => 3,
                'keterangan' => 'Dokumentasi foto halaman 4',
                'created_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],
            [
                'id_dokumentasi' => 11,
                'id_spk' => 2,
                'kategori_foto' => 'list_item',
                'path_foto' => 'output/images/list_item_hal7.jpg',
                'urutan' => 4,
                'keterangan' => 'List item halaman 7',
                'created_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],

            // ========================================
            // DOKUMENTASI SPK 5: DISMANTLE BNI (4 foto)
            // ========================================
            [
                'id_dokumentasi' => 12,
                'id_spk' => 5,
                'kategori_foto' => 'hasil_dismantle',
                'path_foto' => 'output/images/hasil_pekerjaan_cabut_hal2.jpg',
                'urutan' => 1,
                'keterangan' => 'Hasil pekerjaan cabut halaman 2',
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
            [
                'id_dokumentasi' => 13,
                'id_spk' => 5,
                'kategori_foto' => 'hasil_dismantle',
                'path_foto' => 'output/images/hasil_pekerjaan_cabut_hal3.jpg',
                'urutan' => 2,
                'keterangan' => 'Hasil pekerjaan cabut halaman 3',
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
            [
                'id_dokumentasi' => 14,
                'id_spk' => 5,
                'kategori_foto' => 'hasil_dismantle',
                'path_foto' => 'output/images/hasil_pekerjaan_cabut_hal4.jpg',
                'urutan' => 3,
                'keterangan' => 'Hasil pekerjaan cabut halaman 4',
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],
            [
                'id_dokumentasi' => 15,
                'id_spk' => 5,
                'kategori_foto' => 'list_item',
                'path_foto' => 'output/images/dismantle_items_hal5.jpg',
                'urutan' => 4,
                'keterangan' => 'Dismantle items halaman 5',
                'created_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],

            // ========================================
            // DOKUMENTASI SPK 6: SURVEY MLT (10 foto)
            // ========================================
            [
                'id_dokumentasi' => 16,
                'id_spk' => 6,
                'kategori_foto' => 'foto_dokumentasi_umum',
                'path_foto' => 'output/images/dokumentasi_foto_hal5.jpg',
                'urutan' => 1,
                'keterangan' => 'Dokumentasi foto halaman 5',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 17,
                'id_spk' => 6,
                'kategori_foto' => 'foto_dokumentasi_umum',
                'path_foto' => 'output/images/dokumentasi_foto_hal6.jpg',
                'urutan' => 2,
                'keterangan' => 'Dokumentasi foto halaman 6',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 18,
                'id_spk' => 6,
                'kategori_foto' => 'foto_penempatan_perangkat',
                'path_foto' => 'output/images/foto_penempatan_perangkat_di_lokasi_pelanggan_hal7.jpg',
                'urutan' => 3,
                'keterangan' => 'Foto penempatan perangkat di lokasi pelanggan',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 19,
                'id_spk' => 6,
                'kategori_foto' => 'foto_jalur_kabel',
                'path_foto' => 'output/images/foto_jalur_kabel_dalam_gedung_hal8.jpg',
                'urutan' => 4,
                'keterangan' => 'Foto jalur kabel dalam gedung',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 20,
                'id_spk' => 6,
                'kategori_foto' => 'plan_jalur_gedung',
                'path_foto' => 'output/images/plan_jalur_dalam_gedung_hal9.jpg',
                'urutan' => 5,
                'keterangan' => 'Plan jalur dalam gedung',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 21,
                'id_spk' => 6,
                'kategori_foto' => 'data_jalur_kabel',
                'path_foto' => 'output/images/data_jalur_kabel_hal10.jpg',
                'urutan' => 6,
                'keterangan' => 'Data jalur kabel',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 22,
                'id_spk' => 6,
                'kategori_foto' => 'foto_splitter',
                'path_foto' => 'output/images/foto_splitter_hal11.jpg',
                'urutan' => 7,
                'keterangan' => 'Foto splitter halaman 11',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 23,
                'id_spk' => 6,
                'kategori_foto' => 'foto_splitter',
                'path_foto' => 'output/images/foto_splitter_hal12.jpg',
                'urutan' => 8,
                'keterangan' => 'Foto splitter halaman 12',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 24,
                'id_spk' => 6,
                'kategori_foto' => 'foto_hh_eksisting',
                'path_foto' => 'output/images/foto_hh_eksisting_yang_dipakai_hal13.jpg',
                'urutan' => 9,
                'keterangan' => 'Foto handhole eksisting yang dipakai',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
            [
                'id_dokumentasi' => 25,
                'id_spk' => 6,
                'kategori_foto' => 'foto_hh_baru',
                'path_foto' => 'output/images/foto_lokasi_hh_baru_hal14.jpg',
                'urutan' => 10,
                'keterangan' => 'Foto lokasi handhole baru',
                'created_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
        ];

        DB::table('Dokumentasi_Foto')->insert($dokumentasi);
    }
}
