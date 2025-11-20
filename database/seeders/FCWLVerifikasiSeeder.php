<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLVerifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verifikasi = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'role' => 'pelaksana',
                'nama' => 'Muhammad Rizki',
                'tanggal' => '2021-06-22',
                'ttd' => 'signatures/fcwl1_pelaksana_rizki.png',
            ],
            [
                'id_fcwl' => 1,
                'role' => 'pelanggan',
                'nama' => 'Pak Arfan',
                'tanggal' => '2021-06-22',
                'ttd' => 'signatures/fcwl1_pelanggan_arfan.png',
            ],
            [
                'id_fcwl' => 1,
                'role' => 'verifikator',
                'nama' => 'Siti Nurhaliza',
                'tanggal' => '2021-06-22',
                'ttd' => 'signatures/fcwl1_verifikator_siti.png',
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'role' => 'pelaksana',
                'nama' => 'TAUFIQ RAMDAN',
                'tanggal' => '2021-03-03',
                'ttd' => 'signatures/fcwl2_pelaksana_taufiq.png',
            ],
            [
                'id_fcwl' => 2,
                'role' => 'pelanggan',
                'nama' => 'Tio',
                'tanggal' => '2021-03-03',
                'ttd' => 'signatures/fcwl2_pelanggan_tio.png',
            ],
            [
                'id_fcwl' => 2,
                'role' => 'verifikator',
                'nama' => 'Andi Wijaya',
                'tanggal' => '2021-03-03',
                'ttd' => 'signatures/fcwl2_verifikator_andi.png',
            ],
        ];

        DB::table('fcwl_verifikasi')->insert($verifikasi);
        
        $this->command->info('✓ FCWL_Verifikasi seeded: 6 records (3 per FCWL)');
    }
}