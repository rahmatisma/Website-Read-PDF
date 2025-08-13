<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TandaTanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Tanda_Tangan')->insert([
            [
                'id_ttd' => 1,
                'id_spk' => 1,
                'peran' => 'Supervisor',
                'nama' => 'Budi Santoso',
                'path_ttd' => 'uploads/ttd/budi_santoso.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_ttd' => 2,
                'id_spk' => 1,
                'peran' => 'Teknisi',
                'nama' => 'Andi Wijaya',
                'path_ttd' => 'uploads/ttd/andi_wijaya.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
