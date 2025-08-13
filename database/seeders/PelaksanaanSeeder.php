<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelaksanaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Pelaksanaan')->insert([
            [
                'id_pelaksanaan' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'waktu_permintaan' => '2025-08-13 08:00:00',
                'waktu_datang' => '2025-08-13 09:00:00',
                'waktu_selesai' => '2025-08-13 12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pelaksanaan' => 2,
                'id_spk' => 2,
                'waktu_permintaan' => '2025-08-14 10:00:00',
                'waktu_datang' => '2025-08-14 11:00:00',
                'waktu_selesai' => '2025-08-14 14:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
