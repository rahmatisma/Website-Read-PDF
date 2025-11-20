<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKDataSplitterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data splitter untuk jaringan fiber optic (untuk SPK Survey)
     */
    public function run(): void
    {
        $data_splitter = [
            // ========================================
            // DATA SPLITTER SPK 6: SURVEY MLT
            // Arah akses ke splitter/handhole
            // ========================================
            [
                'id_splitter' => 1,
                'id_spk' => 6,
                'lokasi_splitter' => null,
                'id_splitter_text' => null,
                'kapasitas_splitter' => null,
                'jumlah_port_kosong' => null,
                'list_port_kosong_dan_redaman' => null,
                'nama_node_jika_tidak_ada_splitter' => null,
                'list_port_kosong' => null,
                'arah_akses' => 'Diarahkan ke splitter / handhole splitter',
            ],
        ];

        DB::table('SPK_Data_Splitter')->insert($data_splitter);
    }
}