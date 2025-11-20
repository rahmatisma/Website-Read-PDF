<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWVerifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verifikasi = [
            // FCW #1 - BNI Aktivasi (078890/WO-LA/2021)
            [
                'id_fcw' => 1,
                'role' => 'pelaksana',
                'nama' => 'DENI FIQRI DIKTA',
                'tanggal' => '2021-05-20',
                'ttd' => 'signatures/fcw1_pelaksana_deni.png',
            ],
            [
                'id_fcw' => 1,
                'role' => 'pelanggan',
                'nama' => 'Arya Gusmanda Zulfikri',
                'tanggal' => '2021-05-20',
                'ttd' => 'signatures/fcw1_pelanggan_arya.png',
            ],
            [
                'id_fcw' => 1,
                'role' => 'verifikator',
                'nama' => 'Budi Santoso',
                'tanggal' => '2021-05-20',
                'ttd' => 'signatures/fcw1_verifikator_budi.png',
            ],
            
            // FCW #2 - Multimedia Survey (215164/WO-LA/2023)
            [
                'id_fcw' => 2,
                'role' => 'pelaksana',
                'nama' => 'Firman Gustomi',
                'tanggal' => '2023-11-13',
                'ttd' => 'signatures/fcw2_pelaksana_firman.png',
            ],
            [
                'id_fcw' => 2,
                'role' => 'pelanggan',
                'nama' => 'ARIS',
                'tanggal' => '2023-11-13',
                'ttd' => 'signatures/fcw2_pelanggan_aris.png',
            ],
            [
                'id_fcw' => 2,
                'role' => 'verifikator',
                'nama' => 'Ahmad Wijaya',
                'tanggal' => '2023-11-13',
                'ttd' => 'signatures/fcw2_verifikator_ahmad.png',
            ],
        ];

        DB::table('fcw_verifikasi')->insert($verifikasi);
        
        $this->command->info('✓ FCW_Verifikasi seeded: 6 records (3 per FCW)');
    }
}