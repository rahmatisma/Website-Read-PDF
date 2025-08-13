<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk')->insert([
            [
                'no_spk' => '241371/WO-LA/2024',
                'tanggal_spk' => '2024-05-22',
                'tipe_spk' => 'Dismantle',
                'id_jaringan' => 1,
                'no_mr' => '005852/MR-LA/2024',
                'no_fps' => '',
                'id_vendor' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_spk' => 'SPK-002',
                'tanggal_spk' => '2025-01-20',
                'tipe_spk' => 'Maintenance',
                'id_jaringan' => 2,
                'no_mr' => 'MR-002',
                'no_fps' => 'FPS-002',
                'id_vendor' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
