<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FCWLIndoorAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indoorArea = [
            // FCWL #1 - BNI Wireless Maintenance
            [
                'id_fcwl' => 1,
                'merk_ups' => 'APC Smart-UPS',
                'kapasitas_ups' => '2000 VA',
                'jenis_ups' => 'Sinus, Continu',
                'ruangan_bebas_debu' => 'Ya',
                'suhu_ruangan' => '24°C',
                'terpasang_ground_bar' => 'Ya',
                'catuan_input_modem' => 'UPS',
                'v_input_modem_p_n' => '225.8 VAC',
                'v_input_modem_n_g' => '0.151 VAC',
                'bertumpuk' => 'Ya',
                'lokasi_ruang' => 'Ruang Server Lantai 1',
                'suhu_casing_modem' => 'Normal (tidak panas)',
                'catuan_input_terbounding' => 'Ya, kencang',
                'splicing_konektor_kabel' => 'Baik, rapi',
                'pemilik_perangkat_cpe' => 'Pelanggan',
                'jenis_perangkat_cpe' => 'Router + Modem Wireless',
            ],
            
            // FCWL #2 - BNI Wireless Installation
            [
                'id_fcwl' => 2,
                'merk_ups' => 'Prolink PRO2000SFC',
                'kapasitas_ups' => '2000 VA',
                'jenis_ups' => 'Sinus',
                'ruangan_bebas_debu' => 'Ya',
                'suhu_ruangan' => '23°C',
                'terpasang_ground_bar' => 'Ya',
                'catuan_input_modem' => 'PLN + UPS Backup',
                'v_input_modem_p_n' => '220.5 VAC',
                'v_input_modem_n_g' => '0.082 VAC',
                'bertumpuk' => 'Tidak',
                'lokasi_ruang' => 'Ruang Teknis Lantai 2',
                'suhu_casing_modem' => 'Normal',
                'catuan_input_terbounding' => 'Ya, terpasang dengan baik',
                'splicing_konektor_kabel' => 'Rapat, Baik',
                'pemilik_perangkat_cpe' => 'Lintasarta',
                'jenis_perangkat_cpe' => 'Wireless Modem + Antenna',
            ],
        ];

        DB::table('fcwl_indoor_area')->insert($indoorArea);
        
        $this->command->info('✓ FCWL_Indoor_Area seeded: 2 records');
    }
}