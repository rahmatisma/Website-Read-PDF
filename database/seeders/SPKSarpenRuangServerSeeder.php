<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKSarpenRuangServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Sarana Penunjang Ruang Server (hanya untuk SPK Survey)
     */
    public function run(): void
    {
        $sarpen = [
            // ========================================
            // SARPEN SPK 6: SURVEY MLT
            // Data lengkap kondisi ruang server
            // ========================================
            [
                'id_sarpen' => 1,
                'id_spk' => 6,
                'power_line_listrik' => null,
                'ketersediaan_power_outlet' => 'Ada',
                'grounding_listrik' => 'ada',
                'ups' => 'tersedia',
                'ruangan_ber_ac' => 'ada',
                'suhu_ruangan_value' => 20.0,
                'suhu_ruangan_keterangan' => 'lebih kecil 20°C',
                'lantai' => null,
                'ruang' => 'Ada rak khusus',
                'perangkat_pelanggan' => 'Sudah Ada',
            ],
        ];

        DB::table('SPK_Sarpen_Ruang_Server')->insert($sarpen);
    }
}