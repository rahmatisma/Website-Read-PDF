<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SPKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data SPK: Instalasi, Aktivasi, Dismantle, Survey, FCW, FCWL
     */
    public function run(): void
    {
        $spk = [
            // ========================================
            // SPK 1: INSTALASI - BNI (2021242440)
            // Timeline: 03 Maret 2021 - Instalasi Fisik Perangkat
            // ========================================
            [
                'id_spk' => 1,
                'no_spk' => '065752/WO-LA/2021',
                'no_jaringan' => '2021242440',
                'document_type' => 'spk',
                'jenis_spk' => 'instalasi',
                'tanggal_spk' => '2021-03-03',
                'no_mr' => null,
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2021-03-03 15:00:00'),
                'updated_at' => Carbon::parse('2021-03-03 15:40:00'),
            ],

            // ========================================
            // SPK 2: AKTIVASI - BNI (2021242440)
            // Timeline: 03 Maret 2021 - Aktivasi Layanan setelah Instalasi
            // ========================================
            [
                'id_spk' => 2,
                'no_spk' => '065848/WO-LA/2021',
                'no_jaringan' => '2021242440',
                'document_type' => 'spk',
                'jenis_spk' => 'aktivasi',
                'tanggal_spk' => '2021-03-03',
                'no_mr' => null,
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2021-03-03 16:38:00'),
                'updated_at' => Carbon::parse('2021-03-04 09:55:00'),
            ],

            // ========================================
            // SPK 3: FORM CHECKLIST WIRELINE - BNI (2021242440)
            // Timeline: 20 Mei 2021 - Maintenance Wireline
            // ========================================
            [
                'id_spk' => 3,
                'no_spk' => '078890/WO-LA/2021',
                'no_jaringan' => '2021242440',
                'document_type' => 'form_checklist_wireline',
                'jenis_spk' => 'maintenance',
                'tanggal_spk' => '2021-05-20',
                'no_mr' => null,
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2021-05-20 09:21:00'),
                'updated_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],

            // ========================================
            // SPK 4: FORM CHECKLIST WIRELESS - BNI (2021242440)
            // Timeline: 21 Juni 2021 - Maintenance Wireless
            // ========================================
            [
                'id_spk' => 4,
                'no_spk' => '083806/WO-LA/2021',
                'no_jaringan' => '2021242440',
                'document_type' => 'form_checklist_wireless',
                'jenis_spk' => 'maintenance',
                'tanggal_spk' => '2021-06-21',
                'no_mr' => null,
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2021-06-21 18:00:00'),
                'updated_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],

            // ========================================
            // SPK 5: DISMANTLE - BNI (2021242440)
            // Timeline: 11 September 2025 - Pembongkaran Perangkat
            // ========================================
            [
                'id_spk' => 5,
                'no_spk' => '311436/WO-LA/2025',
                'no_jaringan' => '2021242440',
                'document_type' => 'spk',
                'jenis_spk' => 'dismantle',
                'tanggal_spk' => '2025-09-11',
                'no_mr' => '012853/MR-LA/2025',
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2025-09-11 15:00:00'),
                'updated_at' => Carbon::parse('2025-09-12 03:16:00'),
            ],

            // ========================================
            // SPK 6: SURVEY - MLT (2023390898)
            // Timeline: 09 November 2023 - Survey Lokasi
            // ========================================
            [
                'id_spk' => 6,
                'no_spk' => '215164/WO-LA/2023',
                'no_jaringan' => '2023390898',
                'document_type' => 'spk',
                'jenis_spk' => 'survey',
                'tanggal_spk' => '2023-11-09',
                'no_mr' => null,
                'no_fps' => null,
                'is_deleted' => false,
                'deleted_at' => null,
                'deleted_by' => null,
                'deletion_reason' => null,
                'created_at' => Carbon::parse('2023-11-09 11:00:00'),
                'updated_at' => Carbon::parse('2023-11-13 10:02:00'),
            ],
        ];

        DB::table('SPK')->insert($spk);
    }
}
