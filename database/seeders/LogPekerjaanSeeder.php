<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Log_Pekerjaan')->insert([
            [
                'id_log' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'waktu' => '2025-08-13 09:30:00',
                'aktivitas' => 'Persiapan peralatan dan pengecekan kelengkapan',
                'pelaksana' => 'Budi Santoso',
                'keterangan' => 'Semua peralatan dalam kondisi siap pakai',
                'path_foto' => 'uploads/log_pekerjaan/1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_log' => 2,
                'id_spk' => 1,
                'waktu' => '2025-08-13 10:15:00',
                'aktivitas' => 'Instalasi modem dan konfigurasi awal',
                'pelaksana' => 'Andi Wijaya',
                'keterangan' => 'Instalasi berjalan lancar, koneksi internet stabil',
                'path_foto' => 'uploads/log_pekerjaan/2.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
