<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKJaringanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_jaringan')->insert([
            [
                'id_jaringan' => 1,
                'id_spk' => 1, // SPK Aktivasi
                'no_jaringan' => '2021242440',
                'jasa' => 'LA_IPVPN',
                'manage_router' => 'No',
                'opsi_router' => 'Customer Managed',
                'ip_lan' => '192.168.1.1',
                'tgl_rfs_la' => '2021-03-04',
                'tgl_rfs_plg' => '2021-03-04',
                'kode_jaringan' => 'LA-JKT-001',
                'no_fmb' => 'FMB-2021-001',
                'jenis_aktivasi' => 'ETHERNET',
                'jenis_instalasi' => null,
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream / Upstream TSEL 3GB',
            ],
            [
                'id_jaringan' => 2,
                'id_spk' => 2, // SPK Dismantle
                'no_jaringan' => '2021242440',
                'jasa' => 'LA_IPVPN',
                'manage_router' => 'No',
                'opsi_router' => 'Customer Managed',
                'ip_lan' => '192.168.1.1',
                'tgl_rfs_la' => '2025-09-04',
                'tgl_rfs_plg' => '2025-09-04',
                'kode_jaringan' => 'LA-JKT-001',
                'no_fmb' => 'FMB-2025-002',
                'jenis_aktivasi' => null,
                'jenis_instalasi' => null,
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream',
            ],
            [
                'id_jaringan' => 3,
                'id_spk' => 3, // SPK Instalasi
                'no_jaringan' => '2021242440',
                'jasa' => 'LA_IPVPN',
                'manage_router' => 'No',
                'opsi_router' => 'Customer Managed',
                'ip_lan' => '192.168.1.1',
                'tgl_rfs_la' => '2021-03-03',
                'tgl_rfs_plg' => '2021-03-03',
                'kode_jaringan' => 'LA-JKT-001',
                'no_fmb' => 'FMB-2021-003',
                'jenis_aktivasi' => null,
                'jenis_instalasi' => 'ETHERNET',
                'media_akses' => 'ETHERNET',
                'pop' => null,
                'kecepatan' => 'Downstream / Upstream TSEL 3GB',
            ],
            [
                'id_jaringan' => 4,
                'id_spk' => 4, // SPK Survey
                'no_jaringan' => '2023390898',
                'jasa' => 'LA_METRO_ETHERNET',
                'manage_router' => 'No',
                'opsi_router' => 'Lintasarta Managed',
                'ip_lan' => '10.10.10.1',
                'tgl_rfs_la' => null,
                'tgl_rfs_plg' => '2023-12-01',
                'kode_jaringan' => 'LA-JKT-004',
                'no_fmb' => 'FMB-2023-004',
                'jenis_aktivasi' => null,
                'jenis_instalasi' => null,
                'media_akses' => 'FO',
                'pop' => '1. JKTRMCSR01 - 446285 (ADPKRMC01)',
                'kecepatan' => 'Downstream / Upstream 1000 Mbps',
            ],
        ]);
    }
}