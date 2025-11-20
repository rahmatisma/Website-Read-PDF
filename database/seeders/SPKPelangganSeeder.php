<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_pelanggan')->insert([
            [
                'id_pelanggan' => 1,
                'id_spk' => 1, // SPK Aktivasi
                'nama_pelanggan' => 'BANK NEGARA INDONESIA 1946 (PERSERO)',
                'lokasi_pelanggan' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'kontak_person' => 'Pak Arya',
                'telepon' => '0215728563',
            ],
            [
                'id_pelanggan' => 2,
                'id_spk' => 2, // SPK Dismantle
                'nama_pelanggan' => 'BANK NEGARA INDONESIA 1946 (PERSERO)',
                'lokasi_pelanggan' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'kontak_person' => 'KETUT SARJANA',
                'telepon' => '02129946000',
            ],
            [
                'id_pelanggan' => 3,
                'id_spk' => 3, // SPK Instalasi
                'nama_pelanggan' => 'BANK NEGARA INDONESIA 1946 (PERSERO)',
                'lokasi_pelanggan' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'kontak_person' => 'TUQINO',
                'telepon' => '0215728563',
            ],
            [
                'id_pelanggan' => 4,
                'id_spk' => 4, // SPK Survey
                'nama_pelanggan' => 'MULTIMEDIA LINK TECHNOLOGY',
                'lokasi_pelanggan' => 'JL. GRIYA ALAM SENTOSA',
                'kontak_person' => 'ARIS',
                'telepon' => '085219876108',
            ],
        ]);
    }
}