<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKExecutionInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data eksekusi: koordinat, PIC, teknisi, vendor
     */
    public function run(): void
    {
        $execution = [
            // ========================================
            // EXECUTION SPK 1: INSTALASI BNI
            // Vendor: DIKTA TEKNOLOGI
            // ========================================
            [
                'id_execution' => 1,
                'id_spk' => 1,
                'latitude' => -6.24854,
                'longitude' => 106.98937,
                'pic_pelanggan' => 'tio',
                'kontak_pic_pelanggan' => '085808894952',
                'teknisi' => 'TAUFIQ RAMDAN',
                'nama_vendor' => 'DIKTA TEKNOLOGI',
            ],

            // ========================================
            // EXECUTION SPK 2: AKTIVASI BNI
            // Vendor: DIKTA TEKNOLOGI (sama dengan instalasi)
            // ========================================
            [
                'id_execution' => 2,
                'id_spk' => 2,
                'latitude' => -6.24854,
                'longitude' => 106.98937,
                'pic_pelanggan' => 'tio',
                'kontak_pic_pelanggan' => '085808894952',
                'teknisi' => 'TAUFIQ RAMDAN',
                'nama_vendor' => 'DIKTA TEKNOLOGI',
            ],

            // ========================================
            // EXECUTION SPK 3: FCW BNI
            // Vendor: DIKTA (dari verifikasi form)
            // ========================================
            [
                'id_execution' => 3,
                'id_spk' => 3,
                'latitude' => null,
                'longitude' => null,
                'pic_pelanggan' => 'Pak Arya',
                'kontak_pic_pelanggan' => '+62 895-3320-85507',
                'teknisi' => 'DENI FIQRI',
                'nama_vendor' => 'DIKTA',
            ],

            // ========================================
            // EXECUTION SPK 4: FCWL BNI
            // Vendor: (tidak disebutkan dalam form)
            // ========================================
            [
                'id_execution' => 4,
                'id_spk' => 4,
                'latitude' => null,
                'longitude' => null,
                'pic_pelanggan' => 'Pak Arfan',
                'kontak_pic_pelanggan' => '+62 812-9024-5887',
                'teknisi' => 'Teknisi Wireless',
                'nama_vendor' => 'DIKTA',
            ],

            // ========================================
            // EXECUTION SPK 5: DISMANTLE BNI
            // Vendor: AVARA WIRA BAKTI (vendor berbeda)
            // ========================================
            [
                'id_execution' => 5,
                'id_spk' => 5,
                'latitude' => null,
                'longitude' => null,
                'pic_pelanggan' => 'rusdy',
                'kontak_pic_pelanggan' => '6289603298358',
                'teknisi' => 'Muhammad Stanza AWB',
                'nama_vendor' => 'AVARA WIRA BAKTI',
            ],

            // ========================================
            // EXECUTION SPK 6: SURVEY MLT
            // Vendor: DS3
            // ========================================
            [
                'id_execution' => 6,
                'id_spk' => 6,
                'latitude' => -6.39541,
                'longitude' => 106.88371,
                'pic_pelanggan' => '.',
                'kontak_pic_pelanggan' => '0',
                'teknisi' => 'Firman Gustomi',
                'nama_vendor' => 'DS3',
            ],
        ];

        DB::table('SPK_Execution_Info')->insert($execution);
    }
}
