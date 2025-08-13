<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PelangganSeeder::class,
            JaringanSeeder::class,
            VendorSeeder::class,
            SpkSeeder::class,
            PerangkatSeeder::class,
            ItemPekerjaanSeeder::class,
            PelaksanaanSeeder::class,
            DokumentasiSeeder::class,
            LogPekerjaanSeeder::class,
            TandaTanganSeeder::class,
            LineChecklistSeeder::class,
            IndoorAreaChecklistSeeder::class,
            DataRemoteSeeder::class,
            DataLokasiSeeder::class,
            ElectricalChecklistSeeder::class,
            EnvironmentChecklistSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
