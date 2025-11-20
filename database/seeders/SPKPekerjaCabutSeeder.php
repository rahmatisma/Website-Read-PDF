<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPKPekerjaCabutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spk_pekerja_cabut')->insert([
            [
                'id_pekerja' => 1,
                'id_spk' => 2, // SPK Dismantle
                'pic_pelanggan' => 'rusdy',
                'kontak_pic_pelanggan' => '6289603298358',
                'teknisi' => 'Muhammad Stanza AWB',
                'nama_vendor' => 'AVARA WIRA BAKTI',
            ],
        ]);
    }
}