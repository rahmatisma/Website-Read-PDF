<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormChecklistWirelessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('form_checklist_wireless')->insert([
            [
                'id_fcwl' => 1,
                'id_spk' => 1, // SPK Aktivasi - BNI (juga punya wireless checklist)
                'no_spk' => '083806/WO-LA/2021',
                'tanggal' => '2021-06-21',
                'kota' => 'Jakarta',
                'propinsi' => 'DKI Jakarta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_fcwl' => 2,
                'id_spk' => 2, // SPK Instalasi - BNI (wireless installation)
                'no_spk' => '065752/WO-LA/2021',
                'tanggal' => '2021-03-03',
                'kota' => 'Bekasi',
                'propinsi' => 'Jawa Barat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        
        $this->command->info('✓ Form_Checklist_Wireless seeded: 2 records');
    }
}