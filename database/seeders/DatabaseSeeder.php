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
            SPKSeeder::class,
            SPKPelangganSeeder::class,
            SPKJaringanSeeder::class,
            SPKPelaksanaanSeeder::class,
            SPKVendorSeeder::class,
            SPKPekerjaCabutSeeder::class,
            // SURVEY BLOCK
            SPKInformasiGedungSeeder::class,
            SPKSarpenRuangServerSeeder::class,
            SPKSarpenTeganganSeeder::class,
            SPKLokasiAntenaSeeder::class,
            SPKPerizinanBiayaGedungSeeder::class,
            SPKPenempatanPerangkatSeeder::class,
            SPKPerizinanBiayaKawasanSeeder::class,
            SPKKawasanUmumSeeder::class,
            SPKDataSplitterSeeder::class,
            SPKHHEksistingSeeder::class,
            SPKHHBaruSeeder::class,

            // COMMON
            DokumentasiFotoSeeder::class,
            BeritaAcaraSeeder::class,
            ListItemSeeder::class,

            // FORM CHECKLIST WIRELINE
            FormChecklistWirelineSeeder::class,
            FCWWaktuPelaksanaanSeeder::class,
            FCWTeganganSeeder::class,
            FCWVerifikasiSeeder::class,
            FCWDataPerangkatSeeder::class,
            FCWLineChecklistSeeder::class,
            FCWGuidanceFotoSeeder::class,
            FCWLogSeeder::class,

            // FORM CHECKLIST WIRELESS
            FormChecklistWirelessSeeder::class,
            FCWLWaktuPelaksanaanSeeder::class,
            FCWLIndoorAreaSeeder::class,
            FCWLOutdoorAreaSeeder::class,
            FCWLPerangkatAntenaSeeder::class,
            FCWLCablingInstallationSeeder::class,
            FCWLVerifikasiSeeder::class,
            FCWLDataPerangkatSeeder::class,
            FCWLGuidanceFotoSeeder::class,
            FCWLLogSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
