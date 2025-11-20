<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormChecklistWirelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Form Checklist Wireline (FCW) untuk maintenance BNI
     */
    public function run(): void
    {
        $fcw = [
            // ========================================
            // FCW SPK 3: MAINTENANCE WIRELINE BNI
            // Contact: Pak Arya, Teknisi: DENI FIQRI DIKTA
            // ========================================
            [
                'id_fcw' => 1,
                'id_spk' => 3,
                'no_spk' => '078890/WO-LA/2021',
                'tanggal' => '2021-05-20',
                'kota' => null,
                'propinsi' => null,
                'latitude' => null,
                'longitude' => null,
                'posisi_modem_di_lt' => null,
                'ruang' => null,
                'grounding_bar_terkoneksi' => 'ya',
                'ac_pendingin_ruangan' => 'ada',
                'suhu_ruangan_perangkat' => 26.0,
                'modem_quality_data' => 'POWER ON (Green), 109/DCD/LINK-WAN ON, TD/TXD/103 Blink, RD/RXD/104 Blink, RTS/105 ON, CTS/106 ON, Alarm LED ON, All STU Modem Quality A, No Counter CRC Error, Tainet Scorpio Connected, Modem FO Optical LED Alarm Green (ON)',
                'created_at' => Carbon::parse('2021-05-20 09:21:00'),
                'updated_at' => Carbon::parse('2021-05-20 12:20:00'),
            ],
        ];

        DB::table('Form_Checklist_Wireline')->insert($fcw);
    }
}