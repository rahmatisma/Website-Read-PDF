<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKSarpenRuangServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_sarpen_ruang_server')->insert([
            [
                'id_sarpen' => 1,
                'id_spk' => 4, // SPK Survey
                'power_line_listrik' => 'PLN',
                'ketersediaan_power_outlet' => 'Ada',
                'grounding_listrik' => 'Ada',
                'ups' => 'Tersedia/Ada',
                'ruangan_ber_ac' => 'Ada',
                'suhu_ruangan' => 'lebih kecil 20°C',
                'lantai' => '2',
                'ruang' => 'Ada rak khusus',
                'perangkat_pelanggan' => 'Sudah Ada',
            ],
        ]);
    }
}