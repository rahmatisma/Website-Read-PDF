<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormChecklistWirelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('form_checklist_wireline')->insert([
            [
                'id_fcw' => 1,
                'id_spk' => 1, // SPK Aktivasi - punya Form Checklist Wireline
                'no_spk' => '078890/WO-LA/2021',
                'tanggal' => '2021-05-20',
                'kota' => 'Jakarta',
                'propinsi' => 'DKI Jakarta',
                'latitude' => '-6.24854',
                'longitude' => '106.98937',
                'posisi_modem_di_lt' => 'Lantai 1',
                'ruang' => 'Ruang Server',
                'grounding_bar_terkoneksi' => 'Ya',
                'ac_pendingin_ruangan' => 'Ada',
                'suhu_ruangan_perangkat' => 26,
                'modem_quality_data' => 'All STU Modem Quality A, No Counter CRC Error',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_fcw' => 2,
                'id_spk' => 4, // SPK Survey - juga punya Form Checklist Wireline
                'no_spk' => '215164/WO-LA/2023',
                'tanggal' => '2023-11-09',
                'kota' => 'Bekasi',
                'propinsi' => 'Jawa Barat',
                'latitude' => '-6.39541',
                'longitude' => '106.88371',
                'posisi_modem_di_lt' => 'Lantai 2',
                'ruang' => 'Ruang IT',
                'grounding_bar_terkoneksi' => 'Ya',
                'ac_pendingin_ruangan' => 'Ada',
                'suhu_ruangan_perangkat' => 24,
                'modem_quality_data' => 'Signal Quality Good, No Error',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}