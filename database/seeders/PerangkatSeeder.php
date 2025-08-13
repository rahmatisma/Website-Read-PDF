<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Perangkat')->insert([
            [
                'id_perangkat' => 1,
                'nama_perangkat' => 'Modem Fiber Optic',
                'no_reg' => 'REG-001',
                'serial_number' => 'SN123456789',
                'spesifikasi' => 'Modem dengan dukungan koneksi hingga 1Gbps, kompatibel dengan FTTH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_perangkat' => 2,
                'nama_perangkat' => 'Router Cisco RV340',
                'no_reg' => 'REG-002',
                'serial_number' => 'SN987654321',
                'spesifikasi' => 'Router gigabit dual WAN dengan dukungan VPN dan firewall canggih',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
