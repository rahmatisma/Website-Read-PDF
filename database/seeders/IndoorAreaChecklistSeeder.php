<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndoorAreaChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Indoor_Area_Checklist')->insert([
            [
                'id_indoor' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'perangkat_modem' => 'ZTE ZXHN F670L',
                'quality_parameter' => 'SNR: 30 dB, Latency: 5 ms',
                'standard' => 'SNR ≥ 25 dB, Latency ≤ 10 ms',
                'nms_engineer' => 'Parameter normal',
                'onsite_teknisi' => 'Semua kabel terhubung dengan baik',
                'perbaikan' => 'Tidak diperlukan',
                'hasil_akhir' => 'Lolos inspeksi',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
