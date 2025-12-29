<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
        // // ========================================
        // // PART 1: MASTER DATA & SPK UTAMA
        // // ========================================
        // $this->call([
        //     JaringanSeeder::class,                    // 1. Master Jaringan (No FK)
        //     SPKSeeder::class,                         // 2. SPK (FK: no_jaringan)
        //     SPKPelaksanaanSeeder::class,              // 3. Pelaksanaan (FK: id_spk)
        //     SPKExecutionInfoSeeder::class,            // 4. Execution Info (FK: id_spk)
        //     SPKInformasiGedungSeeder::class,          // 5. Informasi Gedung (FK: id_spk)
        // ]);

        // // ========================================
        // // PART 2: DETAIL SPK SURVEY
        // // ========================================
        // $this->call([
        //     SPKSarpenRuangServerSeeder::class,        // 6. Sarpen Ruang Server (FK: id_spk)
        //     SPKSarpenTeganganSeeder::class,           // 7. Sarpen Tegangan (FK: id_sarpen)
        //     SPKLokasiAntenaSeeder::class,             // 8. Lokasi Antena (FK: id_spk)
        //     SPKPerizinanBiayaGedungSeeder::class,     // 9. Perizinan Gedung (FK: id_spk)
        //     SPKPenempatanPerangkatSeeder::class,      // 10. Penempatan Perangkat (FK: id_spk)
        // ]);

        // // ========================================
        // // PART 3: DETAIL SPK SURVEY LANJUTAN
        // // ========================================
        // $this->call([
        //     SPKPerizinanBiayaKawasanSeeder::class,    // 11. Perizinan Kawasan (FK: id_spk)
        //     SPKKawasanUmumSeeder::class,              // 12. Kawasan Umum (FK: id_spk)
        //     SPKDataSplitterSeeder::class,             // 13. Data Splitter (FK: id_spk)
        //     SPKHHEksistingSeeder::class,              // 14. HH Eksisting (FK: id_spk)
        //     SPKHHBaruSeeder::class,                   // 15. HH Baru (FK: id_spk)
        // ]);

        // // ========================================
        // // PART 4: DOKUMENTASI & FORM CHECKLIST WIRELINE
        // // ========================================
        // $this->call([
        //     DokumentasiFotoSeeder::class,             // 16. Dokumentasi Foto (FK: id_spk)
        //     BeritaAcaraSeeder::class,                 // 17. Berita Acara (FK: id_spk)
        //     ListItemSeeder::class,                    // 18. List Item (FK: id_spk)
        //     FormChecklistWirelineSeeder::class,       // 19. Form Checklist Wireline (FK: id_spk)
        //     FCWWaktuPelaksanaanSeeder::class,         // 20. FCW Waktu (FK: id_fcw)
        // ]);

        // // ========================================
        // // PART 5: FORM CHECKLIST WIRELINE DETAIL
        // // ========================================
        // $this->call([
        //     FCWTeganganSeeder::class,                 // 21. FCW Tegangan (FK: id_fcw)
        //     FCWDataPerangkatSeeder::class,            // 22. FCW Data Perangkat (FK: id_fcw)
        //     FCWLineChecklistSeeder::class,            // 23. FCW Line Checklist (FK: id_fcw)
        //     FCWGuidanceFotoSeeder::class,             // 24. FCW Guidance Foto (FK: id_fcw)
        //     FCWLogSeeder::class,                      // 25. FCW Log (FK: id_fcw)
        // ]);

        // // ========================================
        // // PART 6: FORM CHECKLIST WIRELESS
        // // ========================================
        // $this->call([
        //     FormChecklistWirelessSeeder::class,       // 26. Form Checklist Wireless (FK: id_spk)
        //     FCWLWaktuPelaksanaanSeeder::class,        // 27. FCWL Waktu (FK: id_fcwl)
        //     FCWLIndoorAreaSeeder::class,              // 28. FCWL Indoor Area (FK: id_fcwl)
        //     FCWLOutdoorAreaSeeder::class,             // 29. FCWL Outdoor Area (FK: id_fcwl)
        //     FCWLPerangkatAntenaSeeder::class,        // 30. FCWL Perangkat Antenna (FK: id_fcwl)
        // ]);
        // // ========================================
        // // PART 7: FORM CHECKLIST WIRELESS DETAIL (FINAL)
        // // ========================================
        // $this->call([
        //     FCWLCablingInstallationSeeder::class,     // 31. FCWL Cabling (FK: id_fcwl)
        //     FCWLDataPerangkatSeeder::class,           // 32. FCWL Data Perangkat (FK: id_fcwl)
        //     FCWLGuidanceFotoSeeder::class,            // 33. FCWL Guidance Foto (FK: id_fcwl)
        //     FCWLLogSeeder::class,                     // 34. FCWL Log (FK: id_fcwl)
        // ]);
    }
}