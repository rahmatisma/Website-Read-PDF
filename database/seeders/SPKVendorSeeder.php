<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_vendor')->insert([
            [
                'id_vendor_detail' => 1,
                'id_spk' => 1, // SPK Aktivasi
                'latitude' => '-6.24854',
                'longitude' => '106.98937',
                'pic_pelanggan' => 'tio',
                'kontak_pic_pelanggan' => '085808894952',
                'teknisi' => 'TAUFIQ RAMDAN',
                'nama_vendor' => 'DIKTA TEKNOLOGI',
            ],
            [
                'id_vendor_detail' => 2,
                'id_spk' => 3, // SPK Instalasi
                'latitude' => '-6.24854',
                'longitude' => '106.98937',
                'pic_pelanggan' => 'tio',
                'kontak_pic_pelanggan' => '085808894952',
                'teknisi' => 'TAUFIQ RAMDAN',
                'nama_vendor' => 'DIKTA TEKNOLOGI',
            ],
            [
                'id_vendor_detail' => 3,
                'id_spk' => 4, // SPK Survey
                'latitude' => '-6.39541',
                'longitude' => '106.88371',
                'pic_pelanggan' => 'ARIS',
                'kontak_pic_pelanggan' => '085219876108',
                'teknisi' => 'Firman Gustomi',
                'nama_vendor' => 'DS3',
            ],
        ]);
    }
}