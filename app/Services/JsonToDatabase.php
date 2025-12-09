<?php
// servis untuk membagi data
namespace App\Services;

use App\Models\Jaringan;
use App\Models\Spk;
use App\Models\SpkPelaksanaan;
use App\Models\SpkExecutionInfo;
use App\Models\SpkInformasiGedung;
use App\Models\SpkSarpenRuangServer;
use App\Models\SpkLokasiAntena;
use App\Models\SpkPerizinanBiayaGedung;
use App\Models\SpkPenempatanPerangkat;
use App\Models\SpkPerizinanBiayaKawasan;
use App\Models\SpkKawasanUmum;
use App\Models\SpkDataSplitter;
use App\Models\SpkHhEksisting;
use App\Models\SpkHhBaru;
use App\Models\DokumentasiFoto;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class JsonToDatabase
{
    /**
     * Main method untuk pecah JSON ke database
     */
    public function process(array $jsonData, int $uploadId)
    {
        DB::beginTransaction();
        
        try {
            // ✅ FIX: Support 2 format dari Flask
            // Format 1 (Langsung): { "dokumentasi": [...], "parsed": { "data": {...} } }
            // Format 2 (Nested):   { "data": { "dokumentasi": [...], "parsed": { "data": {...} } } }
            
            $parsedData = null;
            $dokumentasi = null;
            $jenisSpk = null;
            
            // Cek apakah ada nested "data" di luar
            if (isset($jsonData['data']['parsed']['data'])) {
                // Format 2 (Nested) - dari Flask response wrapper
                $parsedData = $jsonData['data']['parsed']['data'];
                $dokumentasi = $jsonData['data']['dokumentasi'] ?? [];
                $jenisSpk = $jsonData['data']['parsed']['jenis_spk'] ?? 'survey';
                
                Log::info('Using nested data format', ['upload_id' => $uploadId]);
                
            } elseif (isset($jsonData['parsed']['data'])) {
                // Format 1 (Langsung) - dari Python langsung
                $parsedData = $jsonData['parsed']['data'];
                $dokumentasi = $jsonData['dokumentasi'] ?? [];
                $jenisSpk = $jsonData['parsed']['jenis_spk'] ?? 'survey';
                
                Log::info('Using direct format', ['upload_id' => $uploadId]);
                
            } else {
                throw new Exception('Invalid JSON structure: parsed.data not found');
            }
            
            // 1. Insert/Update JARINGAN (gabungkan data jaringan + pelanggan)
            $noJaringan = $this->processJaringan(
                $parsedData['jaringan'] ?? [],
                $parsedData['pelanggan'] ?? []
            );
            
            // 2. Insert SPK (parent)
            $idSpk = $this->processSpk(
                $parsedData['spk'] ?? [],
                $noJaringan,
                $jenisSpk
            );
            
            // 3. Insert data pelaksanaan
            $this->processPelaksanaan($parsedData['pelaksanaan'] ?? [], $idSpk);
            
            // 4. Insert execution info (vendor)
            $this->processExecutionInfo($parsedData['vendor'] ?? [], $idSpk);
            
            // 5. Insert informasi gedung
            $this->processInformasiGedung($parsedData['informasi_gedung'] ?? [], $idSpk);
            
            // 6. Insert sarpen ruang server
            $this->processSarpenRuangServer($parsedData['sarpen_ruang_server'] ?? [], $idSpk);
            
            // 7. Insert lokasi antena
            $this->processLokasiAntena($parsedData['lokasi_antena'] ?? [], $idSpk);
            
            // 8. Insert perizinan biaya gedung
            $this->processPerizinanBiayaGedung($parsedData['perizinan_biaya_gedung'] ?? [], $idSpk);
            
            // 9. Insert penempatan perangkat
            $this->processPenempatanPerangkat($parsedData['penempatan_perangkat'] ?? [], $idSpk);
            
            // 10. Insert perizinan biaya kawasan
            $this->processPerizinanBiayaKawasan($parsedData['perizinan_biaya_kawasan'] ?? [], $idSpk);
            
            // 11. Insert kawasan umum
            $this->processKawasanUmum($parsedData['kawasan_umum'] ?? [], $idSpk);
            
            // 12. Insert data splitter
            $this->processDataSplitter($parsedData['data_splitter'] ?? [], $idSpk);
            
            // 13. Insert HH Eksisting (bisa banyak)
            $this->processHhEksisting($parsedData['data_hh_eksisting'] ?? [], $idSpk);
            
            // 14. Insert HH Baru (bisa banyak)
            $this->processHhBaru($parsedData['data_hh_baru'] ?? [], $idSpk);
            
            // 15. Insert dokumentasi foto (bisa banyak)
            $this->processDokumentasiFoto($dokumentasi, $idSpk);
            
            // 16. Insert berita acara
            $this->processBeritaAcara($parsedData['berita_acara'] ?? [], $idSpk);
            
            DB::commit();
            
            Log::info('JSON successfully processed to database', [
                'upload_id' => $uploadId,
                'id_spk' => $idSpk,
                'no_jaringan' => $noJaringan
            ]);
            
            return [
                'success' => true,
                'id_spk' => $idSpk,
                'no_jaringan' => $noJaringan
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process JSON to database', [
                'upload_id' => $uploadId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process JARINGAN table
     * ✅ FIX: Gabungkan data jaringan + pelanggan
     */
    private function processJaringan(array $jaringanData, array $pelangganData)
    {
        if (empty($jaringanData['no_jaringan'])) {
            throw new Exception('no_jaringan is required');
        }
        
        $jaringan = Jaringan::updateOrCreate(
            ['no_jaringan' => $jaringanData['no_jaringan']],
            [
                'nama_pelanggan' => $pelangganData['nama_pelanggan'] ?? null,
                'lokasi_pelanggan' => $pelangganData['lokasi_pelanggan'] ?? null,
                'jasa' => $jaringanData['jasa'] ?? null,
                'media_akses' => $jaringanData['media_akses'] ?? null,
                'kecepatan' => $jaringanData['kecepatan'] ?? null,
                'manage_router' => $jaringanData['manage_router'] ?? null,
                'opsi_router' => $jaringanData['opsi_router'] ?? null,
                'ip_lan' => $jaringanData['ip_lan'] ?? null,
                'pop' => $jaringanData['pop'] ?? null,
                'tgl_rfs_la' => $this->parseDate($jaringanData['tgl_rfs_la'] ?? null),
                'tgl_rfs_plg' => $this->parseDate($jaringanData['tgl_rfs_plg'] ?? null),
            ]
        );
        
        return $jaringan->no_jaringan;
    }
    
    /**
     * Process SPK table
     * ✅ FIX: Ambil jenis_spk dari parameter
     */
    private function processSpk(array $spkData, string $noJaringan, string $jenisSpk)
    {
        if (empty($spkData['no_spk'])) {
            throw new Exception('no_spk is required');
        }
        
        $spk = Spk::create([
            'no_spk' => $spkData['no_spk'],
            'no_jaringan' => $noJaringan,
            'document_type' => 'spk',
            'jenis_spk' => $jenisSpk,
            'tanggal_spk' => $this->parseDate($spkData['tanggal_spk'] ?? null),
            'no_fps' => $spkData['no_fps'] ?? null,
        ]);
        
        return $spk->id_spk;
    }
    
    /**
     * Process SPK_Pelaksanaan
     */
    private function processPelaksanaan(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkPelaksanaan::create([
            'id_spk' => $idSpk,
            'permintaan_pelanggan' => $this->parseDateTime($data['permintaan_pelanggan'] ?? null),
            'datang' => $this->parseDateTime($data['datang'] ?? null),
            'selesai' => $this->parseDateTime($data['selesai'] ?? null),
        ]);
    }
    
    /**
     * Process SPK_Execution_Info
     */
    private function processExecutionInfo(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkExecutionInfo::create([
            'id_spk' => $idSpk,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'pic_pelanggan' => $data['pic_pelanggan'] ?? null,
            'kontak_pic_pelanggan' => $data['kontak_pic_pelanggan'] ?? null,
            'teknisi' => $data['teknisi'] ?? null,
            'nama_vendor' => $data['nama_vendor'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Informasi_Gedung
     */
    private function processInformasiGedung(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkInformasiGedung::create([
            'id_spk' => $idSpk,
            'alamat' => $data['alamat'] ?? null,
            'status_gedung' => $data['status_gedung'] ?? null,
            'kondisi_gedung' => $data['kondisi_gedung'] ?? null,
            'pemilik_bangunan' => $data['pemilik_bangunan'] ?? null,
            'kontak_person' => $data['kontak_person'] ?? null,
            'bagian_jabatan' => $data['bagian___jabatan'] ?? null,
            'telpon_fax' => $data['telpon___fax'] ?? null,
            'email' => $data['email'] ?? null,
            'jumlah_lantai_gedung' => $data['jumlah_lantai_gedung'] ?? null,
            'pelanggan_fo' => $data['pelanggan_fo'] ?? null,
            'penempatan_antena' => $data['penempatan_antena'] ?? null,
            'sewa_space_antena' => $data['sewa_space_antena'] ?? null,
            'sewa_shaft_kabel' => $data['sewa_shaft_kabel'] ?? null,
            'biaya_ikg' => $data['biaya_ikg'] ?? null,
            'penanggungjawab_sewa' => $data['penanggungjawab_sewa'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Sarpen_Ruang_Server
     */
    private function processSarpenRuangServer(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        // Map nilai enum
        $groundingListrik = $this->mapAdaTidakAda($data['grounding_listrik'] ?? null);
        $ups = $this->mapTersediaTidak($data['ups'] ?? null);
        $ruanganBerAc = $this->mapAdaTidakAda($data['ruangan_ber_ac'] ?? null);
        
        SpkSarpenRuangServer::create([
            'id_spk' => $idSpk,
            'power_line_listrik' => $data['power_line___listrik'] ?? null,
            'ketersediaan_power_outlet' => $data['ketersediaan_power_outlet_untuk_otb,_modem,_dan_router'] ?? null,
            'grounding_listrik' => $groundingListrik,
            'ups' => $ups,
            'ruangan_ber_ac' => $ruanganBerAc,
            'suhu_ruangan_keterangan' => $data['suhu_ruangan'] ?? null,
            'lantai' => $data['1_lantai'] ?? null,
            'ruang' => $data['2_ruang'] ?? null,
            'perangkat_pelanggan' => $data['perangkat_pelanggan'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Lokasi_Antena
     */
    private function processLokasiAntena(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        $penangkalPetir = $this->mapAdaTidakAda($data['penangkal_petir'] ?? null);
        $towerPole = $this->mapAdaTidakAda($data['tower___pole'] ?? null);
        
        SpkLokasiAntena::create([
            'id_spk' => $idSpk,
            'lokasi_antena' => $data['lokasi_antena'] ?? null,
            'detail_lokasi_antena' => $data['detail_lokasi_antena'] ?? null,
            'space_tersedia' => $data['space_tersedia'] ?? null,
            'akses_di_lokasi_perlu_alat_bantu' => $data['akses_di_lokasi_perlu_alat_bantu'] ?? null,
            'penangkal_petir' => $penangkalPetir,
            'tinggi_penangkal_petir' => $data['tinggi_penangkal_petir'] ?? null,
            'jarak_ke_lokasi_antena' => $data['jarak_ke_lokasi_antena'] ?? null,
            'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
            'tower_pole' => $towerPole,
            'pemilik_tower_pole' => $data['pemilik_tower___pole'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Perizinan_Biaya_Gedung
     */
    private function processPerizinanBiayaGedung(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkPerizinanBiayaGedung::create([
            'id_spk' => $idSpk,
            'pic_bm' => $data['pic_bm'] ?? null,
            'kontak_pic_bm' => $data['kontak_pic_bm'] ?? null,
            'material_dan_infrastruktur' => $data['material_dan_infrastruktur'] ?? null,
            'panjang_kabel_dalam_gedung' => $data['panjang_kabel_dalam_gedung'] ?? null,
            'pelaksana_penarikan_kabel_dalam_gedung' => $data['pelaksana_penarikan_kabel_dalam_gedung'] ?? null,
            'waktu_pelaksanaan_penarikan_kabel' => $data['waktu_pelaksanaan_penarikan_kabel'] ?? null,
            'supervisi' => $data['supervisi'] ?? null,
            'deposit_kerja' => $data['deposit_kerja'] ?? null,
            'ikg_instalasi_kabel_gedung' => $data['ikg_instalasi_kabel_gedung'] ?? null,
            'biaya_sewa' => $data['biaya_sewa'] ?? null,
            'biaya_lain' => $data['biaya_lain…'] ?? null,
            'info_lain_lain_jika_ada' => $data['info_lain___lain_jika_ada'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Penempatan_Perangkat
     */
    private function processPenempatanPerangkat(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        $kesiapanRuang = $this->mapSiapTidakSiap($data['kesiapan_ruang_server'] ?? null);
        $ketersediaanRak = $this->mapAdaTidakAda($data['ketersedian_rak_server'] ?? null);
        $space = $this->mapAdaTidakAda($data['space_modem_dan_router'] ?? null);
        $diizinkan = $this->mapYaTidak($data['diizinkan_foto_ruang_server_pelanggan'] ?? null);
        
        SpkPenempatanPerangkat::create([
            'id_spk' => $idSpk,
            'lokasi_penempatan_modem_dan_router' => $data['lokasi_penempatan_modem_dan_router'] ?? null,
            'kesiapan_ruang_server' => $kesiapanRuang,
            'ketersedian_rak_server' => $ketersediaanRak,
            'space_modem_dan_router' => $space,
            'diizinkan_foto_ruang_server_pelanggan' => $diizinkan,
        ]);
    }
    
    /**
     * Process SPK_Perizinan_Biaya_Kawasan
     */
    private function processPerizinanBiayaKawasan(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        $melewati = $this->mapYaTidak($data['melewati_kawasan_private'] ?? null);
        
        SpkPerizinanBiayaKawasan::create([
            'id_spk' => $idSpk,
            'melewati_kawasan_private' => $melewati ?? 'tidak',
            'nama_kawasan' => $data['nama_kawasan'] ?? null,
            'pic_kawasan' => $data['pic_kawasan'] ?? null,
            'kontak_pic_kawasan' => $data['kontak_pic_kawasan'] ?? null,
            'panjang_kabel_dalam_kawasan' => $data['panjang_kabel_dalam_kawasan'] ?? null,
            'pelaksana_penarikan_kabel_dalam_kawasan' => $data['pelaksana_penarikan_kabel_dalam_kawasan'] ?? null,
            'deposit_kerja' => $data['deposit_kerja'] ?? null,
            'supervisi' => $data['supervisi'] ?? null,
            'biaya_penarikan_kabel_dalam_kawasan' => $data['biaya_penarikan_kabel_dalam_kawasan'] ?? null,
            'biaya_sewa' => $data['biaya_sewa'] ?? null,
            'biaya_lain' => $data['biaya_lain…'] ?? null,
            'info_lain_lain_jika_ada' => $data['info_lain___lain_jika_ada'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Kawasan_Umum
     */
    private function processKawasanUmum(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkKawasanUmum::create([
            'id_spk' => $idSpk,
            'nama_kawasan_umum_pu_yang_dilewati' => $data['nama_kawasan_umum___pu_yang_dilewati'] ?? null,
            'panjang_jalur_outdoor_di_kawasan_umum' => $data['panjang_jalur_outdoor_di_kawasan_umum'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_Data_Splitter
     */
    private function processDataSplitter(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkDataSplitter::create([
            'id_spk' => $idSpk,
            'lokasi_splitter' => $data['lokasi_splitter'] ?? null,
            'id_splitter_text' => $data['id_splitter'] ?? null,
            'kapasitas_splitter' => $data['kapasitas_splitter'] ?? null,
            'jumlah_port_kosong' => $data['jumlah_port_kosong'] ?? null,
            'list_port_kosong_dan_redaman' => $data['list_port_kosong_dan_redaman'] ?? null,
            'nama_node_jika_tidak_ada_splitter' => $data['nama_node_jika_tidak_ada_splitter'] ?? null,
            'list_port_kosong' => $data['list_port_kosong'] ?? null,
            'arah_akses' => $data['arah_akses'] ?? null,
        ]);
    }
    
    /**
     * Process SPK_HH_Eksisting (bisa banyak)
     */
    private function processHhEksisting(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        // Data HH bisa berupa hh_1, hh_2, dst
        foreach ($data as $key => $hh) {
            if (empty($hh) || !is_array($hh)) continue;
            
            // Extract nomor dari key (hh_1 → 1)
            preg_match('/hh_(\d+)/', $key, $matches);
            $nomorHh = $matches[1] ?? 1;
            
            // Skip jika tidak ada data penting
            if (empty($hh['lokasi_hh_' . $nomorHh]) && empty($hh['kondisi_hh_' . $nomorHh])) {
                continue;
            }
            
            SpkHhEksisting::create([
                'id_spk' => $idSpk,
                'nomor_hh' => $nomorHh,
                'kondisi_hh' => $hh['kondisi_hh_' . $nomorHh] ?? null,
                'lokasi_hh' => $hh['lokasi_hh_' . $nomorHh] ?? null,
                'latitude' => $this->extractLatitude($hh['longitude_dan_latitude_hh_' . $nomorHh] ?? null),
                'longitude' => $this->extractLongitude($hh['longitude_dan_latitude_hh_' . $nomorHh] ?? null),
                'ketersediaan_closure' => $hh['ketersediaan_closure_' . $nomorHh] ?? null,
                'kapasitas_closure' => $hh['kapasitas_closure_' . $nomorHh] ?? null,
                'kondisi_closure' => $hh['kondisi_closure_' . $nomorHh] ?? null,
            ]);
        }
    }
    
    /**
     * Process SPK_HH_Baru (bisa banyak)
     */
    private function processHhBaru(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        foreach ($data as $key => $hh) {
            if (empty($hh) || !is_array($hh)) continue;
            
            preg_match('/hh_(\d+)/', $key, $matches);
            $nomorHh = $matches[1] ?? 1;
            
            if (empty($hh['lokasi_hh_' . $nomorHh])) {
                continue;
            }
            
            SpkHhBaru::create([
                'id_spk' => $idSpk,
                'nomor_hh' => $nomorHh,
                'lokasi_hh' => $hh['lokasi_hh_' . $nomorHh] ?? null,
                'latitude' => $this->extractLatitude($hh['longitude_dan_latitude_hh_' . $nomorHh] ?? null),
                'longitude' => $this->extractLongitude($hh['longitude_dan_latitude_hh_' . $nomorHh] ?? null),
                'kebutuhan_penambahan_closure' => $hh['kebutuhan_penambahan_closure_' . $nomorHh] ?? null,
                'kapasitas_closure' => $hh['kapasitas_closure_' . $nomorHh] ?? null,
            ]);
        }
    }
    
    /**
     * Process Dokumentasi_Foto (array)
     */
    private function processDokumentasiFoto(array $dokumentasiArray, int $idSpk)
    {
        if (empty($dokumentasiArray)) return;
        
        foreach ($dokumentasiArray as $index => $foto) {
            if (empty($foto['patch_foto'])) continue;
            
            $kategori = $this->mapKategoriFoto($foto['jenis'] ?? '');
            
            DokumentasiFoto::create([
                'id_spk' => $idSpk,
                'kategori_foto' => $kategori,
                'path_foto' => $foto['patch_foto'],
                'urutan' => $index + 1,
                'keterangan' => $foto['jenis'] ?? null,
            ]);
        }
    }
    
    /**
     * Process Berita_Acara
     */
    private function processBeritaAcara(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        BeritaAcara::create([
            'id_spk' => $idSpk,
            'judul_spk' => $data['judul_spk'] ?? 'BERITA ACARA',
        ]);
    }
    
    // ============================================
    // HELPER METHODS
    // ============================================
    
    private function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '-') return null;
        
        try {
            // Format dari Python: 09/Nov/2023 atau 01-Dec-2023
            // Gunakan createFromFormat untuk handle format ini
            
            // Coba format 1: dd/MMM/yyyy
            $date = \Carbon\Carbon::createFromFormat('d/M/Y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            
            // Coba format 2: dd-MMM-yyyy
            $date = \Carbon\Carbon::createFromFormat('d-M-Y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            
            // Fallback: gunakan Carbon::parse
            $date = \Carbon\Carbon::parse($dateString);
            return $date->format('Y-m-d');
            
        } catch (Exception $e) {
            Log::warning('Failed to parse date', [
                'input' => $dateString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    private function parseDateTime($dateTimeString)
    {
        if (empty($dateTimeString) || $dateTimeString === '-') return null;
        
        try {
            // Format dari Python: 09/Nov/2023   11:00 (ada multiple spaces)
            $dateTimeString = preg_replace('/\s+/', ' ', trim($dateTimeString));
            
            // Coba format 1: dd/MMM/yyyy HH:mm
            $date = \Carbon\Carbon::createFromFormat('d/M/Y H:i', $dateTimeString);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }
            
            // Coba format 2: dd-MMM-yyyy HH:mm
            $date = \Carbon\Carbon::createFromFormat('d-M-Y H:i', $dateTimeString);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }
            
            // Fallback: gunakan Carbon::parse
            $date = \Carbon\Carbon::parse($dateTimeString);
            return $date->format('Y-m-d H:i:s');
            
        } catch (Exception $e) {
            Log::warning('Failed to parse datetime', [
                'input' => $dateTimeString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    private function mapAdaTidakAda($value)
    {
        if (empty($value)) return null;
        
        $lower = strtolower(trim($value));
        if (strpos($lower, 'ada') !== false || strpos($lower, 'tersedia') !== false) {
            return 'ada';
        } elseif (strpos($lower, 'tidak') !== false) {
            return 'tidak_ada';
        }
        return null;
    }
    
    private function mapTersediaTidak($value)
    {
        if (empty($value)) return null;
        
        $lower = strtolower(trim($value));
        if (strpos($lower, 'tersedia') !== false || strpos($lower, 'ada') !== false) {
            return 'tersedia';
        } elseif (strpos($lower, 'tidak') !== false) {
            return 'tidak_tersedia';
        }
        return null;
    }
    
    private function mapSiapTidakSiap($value)
    {
        if (empty($value)) return null;
        
        $lower = strtolower(trim($value));
        if (strpos($lower, 'siap') !== false) {
            return 'siap';
        } elseif (strpos($lower, 'tidak') !== false) {
            return 'tidak_siap';
        }
        return null;
    }
    
    private function mapYaTidak($value)
    {
        if (empty($value)) return null;
        
        $lower = strtolower(trim($value));
        if (strpos($lower, 'ya') !== false) {
            return 'ya';
        } elseif (strpos($lower, 'tidak') !== false) {
            return 'tidak';
        }
        return null;
    }
    
    private function mapKategoriFoto($jenis)
    {
        $jenis = strtolower($jenis);
        
        if (strpos($jenis, 'dokumentasi') !== false) {
            return 'hasil_survey';
        } elseif (strpos($jenis, 'penempatan perangkat') !== false) {
            return 'foto_penempatan_perangkat';
        } elseif (strpos($jenis, 'jalur kabel') !== false) {
            return 'foto_jalur_kabel';
        } elseif (strpos($jenis, 'plan jalur') !== false) {
            return 'plan_jalur_gedung';
        } elseif (strpos($jenis, 'data jalur') !== false) {
            return 'data_jalur_kabel';
        } elseif (strpos($jenis, 'splitter') !== false) {
            return 'foto_splitter';
        } elseif (strpos($jenis, 'hh eksisting') !== false) {
            return 'foto_hh_eksisting';
        } elseif (strpos($jenis, 'hh baru') !== false) {
            return 'foto_hh_baru';
        }
        
        return 'foto_dokumentasi_umum';
    }
    
    private function extractLatitude($latLongString)
    {
        if (empty($latLongString)) return null;
        
        // Format bisa: "lat, long" atau berbagai format lain
        $parts = explode(',', $latLongString);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }
    
    private function extractLongitude($latLongString)
    {
        if (empty($latLongString)) return null;
        
        $parts = explode(',', $latLongString);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }
}