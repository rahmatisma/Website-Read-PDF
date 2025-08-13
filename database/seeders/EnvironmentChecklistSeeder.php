<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnvironmentChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Environment_Checklist')->insert([
            [
                'id_environment' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'ac_pendingin' => true,
                'suhu_ruangan' => 23.5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
