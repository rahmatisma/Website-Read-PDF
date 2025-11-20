<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data log aktivitas untuk Form Checklist Wireless
     * 5 log entries dengan 2 foto
     */
    public function run(): void
    {
        $log = [
            // ========================================
            // LOG FCWL SPK 4: Timeline Maintenance Wireless
            // ========================================
            [
                'id_log' => 1,
                'id_fcwl' => 1,
                'date_time' => Carbon::parse('2021-06-21 19:42:00'),
                'info' => 'Check In',
                'photo' => 'fcwl_log/checkin_hal6.jpg',
                'created_at' => Carbon::parse('2021-06-21 19:42:00'),
            ],
            [
                'id_log' => 2,
                'id_fcwl' => 1,
                'date_time' => Carbon::parse('2021-06-21 19:42:00'),
                'info' => 'update sedang tunggu pic untuk bukan set box atm',
                'photo' => null,
                'created_at' => Carbon::parse('2021-06-21 19:42:00'),
            ],
            [
                'id_log' => 3,
                'id_fcwl' => 1,
                'date_time' => Carbon::parse('2021-06-21 20:19:00'),
                'info' => 'on progres cek modem',
                'photo' => null,
                'created_at' => Carbon::parse('2021-06-21 20:19:00'),
            ],
            [
                'id_log' => 4,
                'id_fcwl' => 1,
                'date_time' => Carbon::parse('2021-06-21 23:01:00'),
                'info' => 'update sedang mendaftarkan katru baru karna kartu lama bermasalah',
                'photo' => null,
                'created_at' => Carbon::parse('2021-06-21 23:01:00'),
            ],
            [
                'id_log' => 5,
                'id_fcwl' => 1,
                'date_time' => Carbon::parse('2021-06-21 23:48:00'),
                'info' => 'setelah penggantian kartu ATM sudah online kembali',
                'photo' => 'fcwl_log/final_hal6.jpg',
                'created_at' => Carbon::parse('2021-06-21 23:48:00'),
            ],
        ];

        DB::table('FCWL_Log')->insert($log);
    }
}