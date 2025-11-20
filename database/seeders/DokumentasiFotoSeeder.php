<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DokumentasiFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dokumentasi_foto')->insert([
            // SPK Aktivasi
            [
                'id_dokumentasi' => 1,
                'id_spk' => 1,
                'jenis' => 'HASIL AKTIVASI',
                'patch_foto' => 'output/images/hasil_aktivasi_hal2.jpg',
                'urutan' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 2,
                'id_spk' => 1,
                'jenis' => 'DOKUMENTASI FOTO',
                'patch_foto' => 'output/images/dokumentasi_foto_hal3.jpg',
                'urutan' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 3,
                'id_spk' => 1,
                'jenis' => 'DOKUMENTASI FOTO',
                'patch_foto' => 'output/images/dokumentasi_foto_hal4.jpg',
                'urutan' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 4,
                'id_spk' => 1,
                'jenis' => 'LIST ITEM',
                'patch_foto' => 'output/images/list_item_hal7.jpg',
                'urutan' => 4,
                'created_at' => Carbon::now(),
            ],
            
            // SPK Dismantle
            [
                'id_dokumentasi' => 5,
                'id_spk' => 2,
                'jenis' => 'HASIL PEKERJAAN CABUT',
                'patch_foto' => 'output/images/hasil_pekerjaan_cabut_hal2.jpg',
                'urutan' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 6,
                'id_spk' => 2,
                'jenis' => 'HASIL PEKERJAAN CABUT',
                'patch_foto' => 'output/images/hasil_pekerjaan_cabut_hal3.jpg',
                'urutan' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 7,
                'id_spk' => 2,
                'jenis' => 'HASIL PEKERJAAN CABUT',
                'patch_foto' => 'output/images/hasil_pekerjaan_cabut_hal4.jpg',
                'urutan' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 8,
                'id_spk' => 2,
                'jenis' => 'DISMANTLE ITEMS',
                'patch_foto' => 'output/images/dismantle_items_hal5.jpg',
                'urutan' => 4,
                'created_at' => Carbon::now(),
            ],
            
            // SPK Instalasi
            [
                'id_dokumentasi' => 9,
                'id_spk' => 3,
                'jenis' => 'HASIL INSTALASI',
                'patch_foto' => 'output/images/hasil_instalasi_hal2.jpg',
                'urutan' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 10,
                'id_spk' => 3,
                'jenis' => 'PHOTO INSTALASI ETHERNET',
                'patch_foto' => 'output/images/photo_instalasi_ethernet_hal3.jpg',
                'urutan' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 11,
                'id_spk' => 3,
                'jenis' => 'PHOTO INSTALASI ETHERNET',
                'patch_foto' => 'output/images/photo_instalasi_ethernet_hal4.jpg',
                'urutan' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 12,
                'id_spk' => 3,
                'jenis' => 'FOTO LAIN-LAIN',
                'patch_foto' => 'output/images/foto_lain-lain_hal6.jpg',
                'urutan' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 13,
                'id_spk' => 3,
                'jenis' => 'LIST ITEM',
                'patch_foto' => 'output/images/list_item_hal8.jpg',
                'urutan' => 5,
                'created_at' => Carbon::now(),
            ],
            
            // SPK Survey
            [
                'id_dokumentasi' => 14,
                'id_spk' => 4,
                'jenis' => 'Dokumentasi foto',
                'patch_foto' => 'output/images/dokumentasi_foto_hal5.jpg',
                'urutan' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 15,
                'id_spk' => 4,
                'jenis' => 'Dokumentasi foto',
                'patch_foto' => 'output/images/dokumentasi_foto_hal6.jpg',
                'urutan' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 16,
                'id_spk' => 4,
                'jenis' => 'Foto penempatan perangkat di lokasi pelanggan',
                'patch_foto' => 'output/images/foto_penempatan_perangkat_hal7.jpg',
                'urutan' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 17,
                'id_spk' => 4,
                'jenis' => 'Foto jalur kabel dalam gedung',
                'patch_foto' => 'output/images/foto_jalur_kabel_hal8.jpg',
                'urutan' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 18,
                'id_spk' => 4,
                'jenis' => 'Plan jalur dalam gedung',
                'patch_foto' => 'output/images/plan_jalur_gedung_hal9.jpg',
                'urutan' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 19,
                'id_spk' => 4,
                'jenis' => 'Data jalur kabel',
                'patch_foto' => 'output/images/data_jalur_kabel_hal10.jpg',
                'urutan' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 20,
                'id_spk' => 4,
                'jenis' => 'Foto splitter',
                'patch_foto' => 'output/images/foto_splitter_hal11.jpg',
                'urutan' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 21,
                'id_spk' => 4,
                'jenis' => 'Foto splitter',
                'patch_foto' => 'output/images/foto_splitter_hal12.jpg',
                'urutan' => 8,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 22,
                'id_spk' => 4,
                'jenis' => 'Foto hh eksisting yang dipakai',
                'patch_foto' => 'output/images/foto_hh_eksisting_hal13.jpg',
                'urutan' => 9,
                'created_at' => Carbon::now(),
            ],
            [
                'id_dokumentasi' => 23,
                'id_spk' => 4,
                'jenis' => 'Foto lokasi hh baru',
                'patch_foto' => 'output/images/foto_hh_baru_hal14.jpg',
                'urutan' => 10,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}