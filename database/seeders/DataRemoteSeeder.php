<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataRemoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Data_Remote')->insert([
            [
                'id_remote' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'kota' => 'Bogor',
                'provinsi' => 'Jawa Barat',
                'jam_perintah' => '2024-05-22 11:17:00',
                'jam_persiapan' => '2024-05-22 12:00:00',
                'jam_berangkat' => '2024-05-22 12:30:00',
                'jam_tiba_lokasi' => '2024-05-22 13:27:00',
                'jam_mulai_kerja' => '2024-05-22 13:30:00',
                'jam_selesai_kerja' => '2024-05-22 14:31:00',
                'jam_pulang' => '2024-05-22 14:35:00',
                'jam_tiba_kantor' => '2024-05-22 15:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
