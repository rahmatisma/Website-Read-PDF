<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DokumentasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Dokumentasi')->insert([
            [
                'id_dokumentasi' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'deskripsi_foto' => 'Foto pemasangan perangkat baru di rak jaringan',
                'path_foto' => 'uploads/dokumentasi/foto1.jpg',
                'tanggal_foto' => '2024-05-22 13:45:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_dokumentasi' => 2,
                'id_spk' => 1,
                'deskripsi_foto' => 'Foto perangkat lama yang dicabut',
                'path_foto' => 'uploads/dokumentasi/foto2.jpg',
                'tanggal_foto' => '2024-05-22 14:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
