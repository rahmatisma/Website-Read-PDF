<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Line_Checklist')->insert([
            [
                'id_checklist' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'checkpoint' => 'Kabel Fiber Optic',
                'line_checklist' => 'FO Backbone',
                'standard' => 'Loss ≤ 0.3 dB per koneksi',
                'existing' => 'Loss 0.25 dB',
                'perbaikan' => 'Tidak diperlukan',
                'hasil_akhir' => 'Sesuai standar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_checklist' => 2,
                'id_spk' => 1,
                'checkpoint' => 'Kabel UTP',
                'line_checklist' => 'LAN Indoor',
                'standard' => 'Konektor RJ45 terpasang rapi',
                'existing' => 'Terdapat 1 konektor longgar',
                'perbaikan' => 'Konektor diganti',
                'hasil_akhir' => 'Normal',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
