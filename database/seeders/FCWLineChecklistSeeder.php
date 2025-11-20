<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FCWLineChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data line checklist untuk Form Checklist Wireline
     * Berisi checkpoint pengecekan kualitas line
     */
    public function run(): void
    {
        $line_checklist = [
            // ========================================
            // SITE AREA CHECKLIST
            // ========================================
            [
                'id_line_check' => 1,
                'id_fcw' => 1,
                'check_point' => 'Site Area - Kabel RJ-11',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 2,
                'id_fcw' => 1,
                'check_point' => 'Site Area - Phone Box',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 3,
                'id_fcw' => 1,
                'check_point' => 'Site Area - KTB/DP Wall',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 4,
                'id_fcw' => 1,
                'check_point' => 'Site Area - DP Telkom',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 5,
                'id_fcw' => 1,
                'check_point' => 'Site Area - Sub Gedung',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 6,
                'id_fcw' => 1,
                'check_point' => 'Site Area - MDF Gedung',
                'standard' => 'No Berkarat, No Bad Contact/Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],

            // ========================================
            // T-LINE CHECKLIST
            // ========================================
            [
                'id_line_check' => 7,
                'id_fcw' => 1,
                'check_point' => 'T-Line Gedung',
                'standard' => 'No Grounded, No Noised, Tdk Putus, Sedikit Terminasi, No Paralel Line Detected, Tahanan Loop: 200-400 Ω',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 8,
                'id_fcw' => 1,
                'check_point' => 'HRB/R.Lintas T-Line (TX,LC)',
                'standard' => 'No Grounded, No Noised, Tdk Putus, Tahanan Loop: 010-100 Ω',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 9,
                'id_fcw' => 1,
                'check_point' => 'Kabel Data',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 10,
                'id_fcw' => 1,
                'check_point' => 'Port Sentral',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],

            // ========================================
            // LINE FO & CONNECTIVITY
            // ========================================
            [
                'id_line_check' => 11,
                'id_fcw' => 1,
                'check_point' => 'Line FO - Cek Signal FO',
                'standard' => 'OK, Signal Balik, Tidak Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 12,
                'id_fcw' => 1,
                'check_point' => 'Koneksi OTB',
                'standard' => 'OK, Tidak Kendor, Tdk Putus',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 13,
                'id_fcw' => 1,
                'check_point' => 'Tes Konektivitas - Bit Error Rate',
                'standard' => 'Durasi 15 Menit, no error',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
            [
                'id_line_check' => 14,
                'id_fcw' => 1,
                'check_point' => 'Tes Konektivitas - Ping',
                'standard' => '100 % reply, < 50 ms',
                'existing' => null,
                'perbaikan' => null,
                'hasil_akhir' => null,
                'created_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
        ];

        DB::table('FCW_Line_Checklist')->insert($line_checklist);
    }
}