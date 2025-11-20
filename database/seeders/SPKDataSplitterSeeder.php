<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKDataSplitterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_data_splitter')->insert([
            [
                'id_splitter' => 1,
                'id_spk' => 4, // SPK Survey
                'lokasi_splitter' => 'ODP Jalan Griya Alam Sentosa',
                'id_splitter_text' => 'SPL-GAS-001',
                'kapasitas_splitter' => '1:8',
                'jumlah_port_kosong' => '3',
                'list_port_kosong_dan_redaman' => 'Port 5: -0.5dB, Port 6: -0.3dB, Port 7: -0.4dB',
                'nama_node_jika_tidak_ada_splitter' => null,
                'list_port_kosong' => 'Port 5, 6, 7',
                'arah_akses' => 'Diarahkan ke splitter / handhole splitter',
            ],
        ]);
    }
}