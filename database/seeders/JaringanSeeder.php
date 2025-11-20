<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JaringanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Master Jaringan untuk pelanggan BNI dan MLT
     */
    public function run(): void
    {
        $jaringan = [
            // ========================================
            // JARINGAN 1: BANK NEGARA INDONESIA 1946 (PERSERO)
            // Digunakan untuk: SPK Instalasi, Aktivasi, Dismantle, FCW, FCWL
            // ========================================
            [
                'no_jaringan' => '2021242440',
                'nama_pelanggan' => 'BANK NEGARA INDONESIA 1946 (PERSERO)',
                'lokasi_pelanggan' => 'JL. CUT NYAK DIEN, KALIMALANG UJUNG',
                'jasa' => 'LA_IPVPN',
                'media_akses' => 'ETHERNET',
                'kecepatan' => 'Downstream / Upstream TSEL 3GB',
                'manage_router' => null,
                'opsi_router' => null,
                'ip_lan' => null,
                'kode_jaringan' => null,
                'no_fmb' => null,
                'pop' => null,
                'tgl_rfs_la' => '2025-09-04',
                'tgl_rfs_plg' => '2025-09-04',
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2021-03-03 15:00:00'),
                'updated_at' => Carbon::parse('2025-09-11 15:00:00'),
            ],

            // ========================================
            // JARINGAN 2: MULTIMEDIA LINK TECHNOLOGY
            // Digunakan untuk: SPK Survey
            // ========================================
            [
                'no_jaringan' => '2023390898',
                'nama_pelanggan' => 'MULTIMEDIA LINK TECHNOLOGY',
                'lokasi_pelanggan' => 'JL. GRIYA ALAM SENTOSA',
                'jasa' => 'LA_METRO_ETHERNET',
                'media_akses' => 'FO',
                'kecepatan' => 'Downstream / Upstream 1000 Mbps',
                'manage_router' => null,
                'opsi_router' => null,
                'ip_lan' => null,
                'kode_jaringan' => null,
                'no_fmb' => null,
                'pop' => '1. JKTRMCSR01 - 446285 (ADPKRMC01)',
                'tgl_rfs_la' => null,
                'tgl_rfs_plg' => '2023-12-01',
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2023-11-09 11:00:00'),
                'updated_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
        ];

        DB::table('JARINGAN')->insert($jaringan);
    }
}

