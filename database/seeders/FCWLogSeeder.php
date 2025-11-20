<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $logs = [
            // FCW #1 - BNI Aktivasi - Timeline sesuai dokumen
            [
                'id_fcw' => 1,
                'date_time' => '2021-05-20 10:45:00',
                'info' => 'Check In',
                'photo' => 'logs/fcw1_checkin.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'date_time' => '2021-05-20 10:46:00',
                'info' => 'Baru sampai lokasi dan nunggu PIC buat buka set box',
                'photo' => 'logs/fcw1_waiting_pic.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'date_time' => '2021-05-20 11:02:00',
                'info' => 'On progress pengecekan',
                'photo' => 'logs/fcw1_checking.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'date_time' => '2021-05-20 11:35:00',
                'info' => 'Temuan modem Huawei up down',
                'photo' => 'logs/fcw1_modem_issue.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'date_time' => '2021-05-20 12:06:00',
                'info' => 'ATM sudah online kembali menggunakan M2M',
                'photo' => 'logs/fcw1_atm_online.jpg',
                'created_at' => $now,
            ],
            
            // FCW #2 - Multimedia Survey - Timeline realistis (sehari kerja)
            [
                'id_fcw' => 2,
                'date_time' => '2023-11-13 09:15:00',
                'info' => 'Check in di lokasi pelanggan, bertemu PIC ARIS',
                'photo' => 'logs/fcw2_checkin.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'date_time' => '2023-11-13 09:30:00',
                'info' => 'Survey ruang server, kondisi ruangan baik, AC tersedia, suhu 24°C',
                'photo' => 'logs/fcw2_survey_ruang.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'date_time' => '2023-11-13 10:15:00',
                'info' => 'Pengecekan jalur kabel dalam gedung, routing feasible via shaft lantai 2',
                'photo' => 'logs/fcw2_jalur_kabel.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'date_time' => '2023-11-13 11:00:00',
                'info' => 'Dokumentasi power outlet, grounding bar, dan rak server',
                'photo' => 'logs/fcw2_dokumentasi.jpg',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'date_time' => '2023-11-13 11:45:00',
                'info' => 'Survey selesai, semua requirement terpenuhi, koordinasi dengan PIC untuk jadwal instalasi',
                'photo' => 'logs/fcw2_survey_selesai.jpg',
                'created_at' => $now,
            ],
        ];

        DB::table('fcw_log')->insert($logs);
        
        $this->command->info('✓ FCW_Log seeded: 10 records (5 for FCW #1, 5 for FCW #2)');
    }
}