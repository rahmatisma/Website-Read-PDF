<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JaringanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jaringan')->insert([
            [
                'no_jaringan' => '2019014946',
                'id_pelanggan' => 1,
                'jenis_layanan' => 'LA_METRO_ETHERNET',
                'media_akses' => 'Radio Link',
                'kecepatan' => 'Downstream / Upstream 1 Mbps',
                'tgl_rfs_la' => '2024-05-21',
                'tgl_rfs_plg' => '2024-05-21',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_jaringan' => 'JRG-002',
                'id_pelanggan' => 2,
                'jenis_layanan' => 'VPN IP',
                'media_akses' => 'Wireless',
                'kecepatan' => '50 Mbps',
                'tgl_rfs_la' => '2025-02-01',
                'tgl_rfs_plg' => '2025-02-03',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
