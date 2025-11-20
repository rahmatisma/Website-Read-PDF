<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKKawasanUmumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_kawasan_umum')->insert([
            [
                'id_kawasan_umum' => 1,
                'id_spk' => 4, // SPK Survey
                'nama_kawasan_umum_pu_yang_dilewati' => 'Jalan Raya Griya Alam Sentosa',
                'panjang_jalur_outdoor_di_kawasan_umum' => '50 meter',
            ],
        ]);
    }
}