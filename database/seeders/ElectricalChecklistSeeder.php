<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElectricalChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Electrical_Checklist')->insert([
            [
                'id_electrical' => 1,
                'id_spk' => 1, // relasi ke tabel SPK
                'p_n_pln' => 220.5,
                'p_n_ups' => 219.8,
                'p_n_it' => 221.0,
                'p_g_pln' => 0.5,
                'p_g_ups' => 0.4,
                'p_g_it' => 0.3,
                'n_g_pln' => 0.2,
                'n_g_ups' => 0.1,
                'n_g_it' => 0.2,
                'grounding_bar_terkoneksi' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
