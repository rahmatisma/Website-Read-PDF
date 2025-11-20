<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $logs = [
            // FCWL #1 - BNI Wireless Maintenance (malam hari)
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-21 19:42:00',
                'info' => 'Check In',
                'photo' => 'logs/fcwl1_checkin.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-21 19:42:00',
                'info' => 'Update sedang tunggu PIC untuk buka set box ATM',
                'photo' => 'logs/fcwl1_waiting_pic.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-21 20:19:00',
                'info' => 'On progress cek modem wireless',
                'photo' => 'logs/fcwl1_checking_modem.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-21 23:01:00',
                'info' => 'Update sedang mendaftarkan kartu baru karena kartu lama bermasalah',
                'photo' => 'logs/fcwl1_kartu_issue.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-21 23:48:00',
                'info' => 'Setelah penggantian kartu ATM sudah online kembali',
                'photo' => 'logs/fcwl1_atm_online.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 1,
                'date_time' => '2021-06-22 00:03:00',
                'info' => 'Testing final, signal strength -18dBm, koneksi stabil',
                'photo' => 'logs/fcwl1_final_test.jpg',
                'created_at' => $now,
            ],
            
            // FCWL #2 - BNI Wireless Installation (sore hari)
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 15:30:00',
                'info' => 'Tim tiba di lokasi, koordinasi dengan PIC Tio',
                'photo' => 'logs/fcwl2_arrival.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 15:45:00',
                'info' => 'Mulai instalasi antenna di rooftop, cuaca cerah',
                'photo' => 'logs/fcwl2_start_install.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 16:20:00',
                'info' => 'Mounting bracket terpasang, mulai aiming antenna ke BTS Bekasi Timur',
                'photo' => 'logs/fcwl2_aiming.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 16:55:00',
                'info' => 'Signal locked, strength -22dBm, instalasi kabel IFL ke indoor unit',
                'photo' => 'logs/fcwl2_signal_locked.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 17:30:00',
                'info' => 'Indoor equipment terpasang, konfigurasi router dan testing konektivitas',
                'photo' => 'logs/fcwl2_indoor_setup.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 18:05:00',
                'info' => 'Testing final: Ping OK (avg 15ms), throughput test 95Mbps, grounding checked',
                'photo' => 'logs/fcwl2_final_testing.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcwl' => 2,
                'date_time' => '2021-03-03 18:20:00',
                'info' => 'Instalasi selesai, dokumentasi lengkap, serah terima dengan PIC',
                'photo' => 'logs/fcwl2_handover.jpg',
                'created_at' => $now,
            ],
        ];

        DB::table('fcwl_log')->insert($logs);
        
        $this->command->info('✓ FCWL_Log seeded: 13 records (6 for FCWL #1, 7 for FCWL #2)');
    }
}