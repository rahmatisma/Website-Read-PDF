<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormChecklistWirelessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Form Checklist Wireless (FCWL) untuk maintenance wireless BNI
     */
    public function run(): void
    {
        $fcwl = [
            // ========================================
            // FCWL SPK 4: MAINTENANCE WIRELESS BNI
            // Contact: Pak Arfan, No. SPK: 083806/WO-LA/2021
            // ========================================
            [
                'id_fcwl' => 1,
                'id_spk' => 4,
                'no_spk' => '083806/WO-LA/2021',
                'tanggal' => '2021-06-21',
                'kota' => null,
                'propinsi' => null,
                'created_at' => Carbon::parse('2021-06-21 18:00:00'),
                'updated_at' => Carbon::parse('2021-06-22 00:03:00'),
            ],
        ];

        DB::table('Form_Checklist_Wireless')->insert($fcwl);
    }
}