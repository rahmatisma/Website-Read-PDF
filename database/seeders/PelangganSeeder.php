<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            [
                'nama_pelanggan' => 'Bank Danamon Indonesia',
                'alamat' => 'Kota Wisata Cluster Concordia Blok SRC No.6 Bank Danamon Indonesia',
                'kontak_person' => 'Achyan',
                'no_telepon' => '021123456',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelanggan' => 'PT Indosat Ooredoo',
                'alamat' => 'Jl. Medan Merdeka Barat No. 21, Jakarta',
                'kontak_person' => 'Siti Rahma',
                'no_telepon' => '021654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
