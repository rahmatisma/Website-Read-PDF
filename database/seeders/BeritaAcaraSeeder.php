<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeritaAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('berita_acara')->insert([
            [
                'id_berita_acara' => 1,
                'id_spk' => 1, // SPK Aktivasi
                'judul_spk' => 'BERITA ACARA',
                'tipe_spk' => 'aktivasi',
                'nomor_spk' => '065848/WO-LA/2021',
                'tanggal' => '2021-03-03',
                'no_fps' => null,
                'jenis_aktivasi' => 'ETHERNET',
                'jenis_instalasi' => null,
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream / Upstream TSEL 3GB',
                'kontak_person' => 'TUQINO',
                'telepon' => '0215728563',
                'permintaan_pelanggan' => '2021-03-03 16:38:00',
                'datang' => '2021-03-03 16:46:00',
                'selesai' => '2021-03-04 09:55:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_berita_acara' => 2,
                'id_spk' => 2, // SPK Dismantle
                'judul_spk' => 'BERITA ACARA',
                'tipe_spk' => 'dismantle',
                'nomor_spk' => '311436/WO-LA/2025',
                'tanggal' => '2025-09-11',
                'no_fps' => null,
                'jenis_aktivasi' => null,
                'jenis_instalasi' => null,
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream',
                'kontak_person' => 'TUQINO',
                'telepon' => '0215728563',
                'permintaan_pelanggan' => '2025-09-11 15:00:00',
                'datang' => '2025-09-11 15:05:00',
                'selesai' => '2025-09-12 03:16:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_berita_acara' => 3,
                'id_spk' => 3, // SPK Instalasi
                'judul_spk' => 'BERITA ACARA',
                'tipe_spk' => 'instalasi',
                'nomor_spk' => '065752/WO-LA/2021',
                'tanggal' => '2021-03-03',
                'no_fps' => null,
                'jenis_aktivasi' => null,
                'jenis_instalasi' => 'ETHERNET',
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream / Upstream TSEL 3GB',
                'kontak_person' => 'TUQINO',
                'telepon' => '0215728563',
                'permintaan_pelanggan' => '2021-03-03 15:00:00',
                'datang' => '2021-03-03 15:22:00',
                'selesai' => '2021-03-03 15:40:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_berita_acara' => 4,
                'id_spk' => 4, // SPK Survey
                'judul_spk' => 'BERITA ACARA',
                'tipe_spk' => 'survey',
                'nomor_spk' => '215164/WO-LA/2023',
                'tanggal' => '2023-11-09',
                'no_fps' => null,
                'jenis_aktivasi' => null,
                'jenis_instalasi' => null,
                'media_akses' => 'FO',
                'pop' => '1. JKTRMCSR01 - 446285 (ADPKRMC01)',
                'kecepatan' => 'Downstream / Upstream 1000 Mbps',
                'kontak_person' => 'ARIS',
                'telepon' => '085219876108',
                'permintaan_pelanggan' => '2023-11-09 11:00:00',
                'datang' => '2023-11-11 17:13:00',
                'selesai' => '2023-11-13 10:02:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}