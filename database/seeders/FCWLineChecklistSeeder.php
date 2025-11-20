<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLineChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $lineChecklist = [
            // FCW #1 - BNI Aktivasi - COMPLETE CHECKLIST
            [
                'id_fcw' => 1,
                'check_point' => 'Kabel RJ-11',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => 'Kondisi baik, tidak berkarat',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Phone Box',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => 'Bersih, koneksi aman',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'KTB/DP Wall',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => 'Terminasi rapi',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'DP Telkom',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => 'Kondisi normal',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'T-Line Gedung',
                'standard' => 'No Grounded, No Noised, Tdk Putus, Tahanan Loop: 200-400 Ω',
                'existing' => 'Tahanan 285 Ω',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK (285 Ω)',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'HRB/R.Lintas T-Line',
                'standard' => 'No Grounded, No Noised, Tdk Putus, Tahanan Loop: 010-100 Ω',
                'existing' => 'Tahanan 45 Ω',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK (45 Ω)',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Kabel Data',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => 'Terpasang dengan baik',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Port Sentral',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => 'Koneksi stabil',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Line FO - Signal',
                'standard' => 'OK, Signal Balik, Tidak Putus',
                'existing' => 'Signal strength -18 dBm',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Koneksi OTB',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => 'Terpasang kencang',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Bit Error Rate Test',
                'standard' => 'Durasi 15 Menit, no error',
                'existing' => 'Test dilakukan 15 menit',
                'perbaikan' => '-',
                'hasil_akhir' => 'PASS (0 error)',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 1,
                'check_point' => 'Ping Test',
                'standard' => '100 % reply, < 50 ms',
                'existing' => '100% reply, avg 12 ms',
                'perbaikan' => '-',
                'hasil_akhir' => 'PASS (12 ms)',
                'created_at' => $now,
            ],
            
            // FCW #2 - Multimedia Survey - SURVEY CHECKLIST (lebih sederhana)
            [
                'id_fcw' => 2,
                'check_point' => 'Kabel Existing',
                'standard' => 'Kondisi fisik kabel baik',
                'existing' => 'Belum ada instalasi',
                'perbaikan' => 'Perlu instalasi baru',
                'hasil_akhir' => 'Noted',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'check_point' => 'Routing Path',
                'standard' => 'Jalur aman dan feasible',
                'existing' => 'Ada jalur melalui shaft gedung',
                'perbaikan' => '-',
                'hasil_akhir' => 'Feasible',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'check_point' => 'Akses Ruang Server',
                'standard' => 'Mudah diakses teknisi',
                'existing' => 'Akses mudah, lantai 2',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'check_point' => 'Power Outlet',
                'standard' => 'Tersedia outlet untuk perangkat',
                'existing' => 'Ada 4 outlet tersedia',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'check_point' => 'Grounding System',
                'standard' => 'Ada grounding bar',
                'existing' => 'Grounding bar tersedia',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
            [
                'id_fcw' => 2,
                'check_point' => 'Rak Server',
                'standard' => 'Ada space untuk modem/router',
                'existing' => 'Rak khusus tersedia, space 3U',
                'perbaikan' => '-',
                'hasil_akhir' => 'OK',
                'created_at' => $now,
            ],
        ];

        DB::table('fcw_line_checklist')->insert($lineChecklist);
        
        $this->command->info('✓ FCW_Line_Checklist seeded: 18 records (12 for FCW #1, 6 for FCW #2)');
    }
}