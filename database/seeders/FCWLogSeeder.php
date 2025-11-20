<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data log aktivitas untuk Form Checklist Wireline
     */
    public function run(): void
    {
        $log = [
            // ========================================
            // LOG FCW SPK 3: Timeline Maintenance
            // ========================================
            [
                'id_log' => 1,
                'id_fcw' => 1,
                'date_time' => Carbon::parse('2021-05-20 10:45:00'),
                'info' => 'Check In',
                'photo' => null,
                'created_at' => Carbon::parse('2021-05-20 10:45:00'),
            ],
            [
                'id_log' => 2,
                'id_fcw' => 1,
                'date_time' => Carbon::parse('2021-05-20 10:46:00'),
                'info' => 'baru sampai lokasi dan nunggu pic buat buka set box',
                'photo' => null,
                'created_at' => Carbon::parse('2021-05-20 10:46:00'),
            ],
            [
                'id_log' => 3,
                'id_fcw' => 1,
                'date_time' => Carbon::parse('2021-05-20 11:02:00'),
                'info' => 'on progres pengecekan',
                'photo' => null,
                'created_at' => Carbon::parse('2021-05-20 11:02:00'),
            ],
            [
                'id_log' => 4,
                'id_fcw' => 1,
                'date_time' => Carbon::parse('2021-05-20 11:35:00'),
                'info' => 'temuan modem hauwey up dwon',
                'photo' => null,
                'created_at' => Carbon::parse('2021-05-20 11:35:00'),
            ],
            [
                'id_log' => 5,
                'id_fcw' => 1,
                'date_time' => Carbon::parse('2021-05-20 12:06:00'),
                'info' => 'ATM sudah online kembali mengunakan m2m',
                'photo' => null,
                'created_at' => Carbon::parse('2021-05-20 12:06:00'),
            ],
        ];

        DB::table('FCW_Log')->insert($log);
    }
}