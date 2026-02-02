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
use App\Models\ListItem;
use App\Services\FormChecklistWirelineService;
use App\Services\FormChecklistWirelessService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class JsonToDatabase
{
    public function process(array $jsonData, int $uploadId)
    {
        DB::beginTransaction();
        
        try {
            $parsedData = null;
            $dokumentasi = null;
            $documentType = null;
            $jenisSpk = null;
            
            // ============================================
            // 🔧 FIX: Support multiple JSON structures
            // ============================================
            
            Log::info('🔍 DEBUGGING JSON STRUCTURE', [
                'upload_id' => $uploadId,
                'top_level_keys' => array_keys($jsonData),
                'has_data_key' => isset($jsonData['data']),
                'has_parsed_key' => isset($jsonData['parsed']),
            ]);
            
            // STRUCTURE 1: SPK & Form Checklist (nested dalam 'data')
            // {
            //   "data": {
            //     "parsed": {
            //       "document_type": "...",
            //       "data": { ... }
            //     },
            //     "dokumentasi": [ ... ]
            //   }
            // }
            if (isset($jsonData['data']['parsed']['data'])) {
                $parsedData = $jsonData['data']['parsed']['data'];
                $dokumentasi = $jsonData['data']['dokumentasi'] ?? [];
                $documentType = $jsonData['data']['parsed']['document_type'] ?? 'unknown';
                $jenisSpk = $jsonData['data']['parsed']['jenis_spk'] ?? null;
                
                Log::info(' JSON Structure 1: SPK/Form Checklist (data.parsed.data)', [
                    'upload_id' => $uploadId,
                    'document_type' => $documentType,
                    'jenis_spk' => $jenisSpk
                ]);
            }
            // STRUCTURE 4: SPK with nested parsed (data.parsed.parsed)
            // {
            //   "data": {
            //     "parsed": {
            //       "document_type": "spk_survey",
            //       "metadata": {...},
            //       "parsed": {        // ← Data asli di sini!
            //         "spk": {...},
            //         "pelanggan": {...}
            //       }
            //     },
            //     "dokumentasi": [...]
            //   }
            // }
            elseif (isset($jsonData['data']['parsed']['parsed'])) {
                $parsedData = $jsonData['data']['parsed']['parsed']; //  Ambil dari data.parsed.parsed
                $dokumentasi = $jsonData['data']['dokumentasi'] ?? [];
                $documentType = $jsonData['data']['parsed']['document_type'] ?? 'unknown';
                $jenisSpk = $this->inferJenisSpk($documentType); // Infer dari document_type
                
                Log::info(' JSON Structure 4: SPK (data.parsed.parsed)', [
                    'upload_id' => $uploadId,
                    'document_type' => $documentType,
                    'jenis_spk' => $jenisSpk,
                    'inferred' => true
                ]);
            }
            // STRUCTURE 2: Form PM POP (flat structure)
            // {
            //   "parsed": {
            //     "document_type": "form_pm_battery",
            //     "parsed": {
            //       "header": { ... },
            //       "informasi_umum": { ... },
            //       ...
            //     }
            //   },
            //   "dokumentasi": [ ... ]
            // }
            elseif (isset($jsonData['parsed']['parsed'])) {
                $parsedData = $jsonData['parsed']['parsed']; //  Ambil dari parsed.parsed
                $dokumentasi = $jsonData['dokumentasi'] ?? [];
                $documentType = $jsonData['parsed']['document_type'] ?? 'unknown';
                $jenisSpk = null; // Form PM POP tidak punya jenis_spk
                
                Log::info(' JSON Structure: Form PM POP (flat)', [
                    'upload_id' => $uploadId,
                    'document_type' => $documentType
                ]);
            }
            // STRUCTURE 3: Legacy (backward compatibility)
            // {
            //   "parsed": {
            //     "data": { ... }
            //   }
            // }
            elseif (isset($jsonData['parsed']['data'])) {
                $parsedData = $jsonData['parsed']['data'];
                $dokumentasi = $jsonData['dokumentasi'] ?? [];
                $documentType = $jsonData['parsed']['document_type'] ?? 'unknown';
                $jenisSpk = $jsonData['parsed']['jenis_spk'] ?? null;
                
                Log::info(' JSON Structure: Legacy', [
                    'upload_id' => $uploadId,
                    'document_type' => $documentType,
                    'jenis_spk' => $jenisSpk
                ]);
            }
            else {
                // Invalid structure - Debug lebih detail
                $debugInfo = [
                    'upload_id' => $uploadId,
                    'json_keys' => array_keys($jsonData),
                ];
                
                if (isset($jsonData['data'])) {
                    $debugInfo['data_keys'] = array_keys($jsonData['data']);
                    if (isset($jsonData['data']['parsed'])) {
                        $debugInfo['data_parsed_keys'] = array_keys($jsonData['data']['parsed']);
                    }
                }
                
                if (isset($jsonData['parsed'])) {
                    $debugInfo['parsed_keys'] = array_keys($jsonData['parsed']);
                }
                
                Log::error('Invalid JSON structure', $debugInfo);
                throw new Exception('Invalid JSON structure: Unable to locate parsed data. Check logs for details.');
            }
            
            Log::info(' Processing JSON to Database', [
                'upload_id' => $uploadId,
                'document_type' => $documentType,
                'jenis_spk' => $jenisSpk,
                'has_data' => !empty($parsedData),
                'data_keys' => is_array($parsedData) ? array_keys($parsedData) : 'not_array'
            ]);
            
            //  ROUTE BERDASARKAN DOCUMENT TYPE
            switch ($documentType) {
                case 'spk_survey':
                case 'spk_instalasi':
                case 'spk_dismantle':
                case 'spk_aktivasi':
                    // Process SPK
                    Log::info('📄 Routing to SPK processing', ['document_type' => $documentType]);
                    $result = $this->processSPK($parsedData, $dokumentasi, $jenisSpk, $uploadId);
                    DB::commit();
                    return $result;
                
                case 'checklist_wireline':
                    // Process Form Checklist Wireline
                    Log::info('Routing to Form Checklist Wireline processing');
                    $result = $this->processFormChecklistWireline($parsedData, $uploadId);
                    DB::commit();
                    return $result;
                
                case 'checklist_wireless':
                    // Process Form Checklist Wireless
                    Log::info('Routing to Form Checklist Wireless processing');
                    $result = $this->processFormChecklistWireless($parsedData, $uploadId);
                    DB::commit();
                    return $result;
                
                //  NEW: Form PM POP types (untuk next phase)
                case 'form_pm_battery':
                case 'form_pm_1phase_ups':
                case 'form_pm_3phase_ups':
                case 'form_pm_rectifier':
                case 'form_pm_inverter':
                case 'form_pm_ruang_shelter':
                case 'form_pm_petir_grounding':
                case 'form_pm_instalasi_kabel':
                case 'form_pm_pole_tower':
                case 'form_pm_ac':
                case 'form_pm_dokumentasi_perangkat':
                case 'form_pm_permohonan_tindak_lanjut':
                    // Process Form PM POP (placeholder for now)
                    Log::info('🔋 Form PM POP detected (not yet implemented)', [
                        'document_type' => $documentType
                    ]);

                    $pmService = new FormPmPopService();
                    $result = $pmService->process($parsedData, $uploadId);
                    
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => 'Form PM POP processing not yet implemented',
                        'document_type' => $documentType
                    ];
                
                default:
                    // Fallback ke SPK processing untuk backward compatibility
                    Log::warning('⚠️ Unknown document type, fallback to SPK processing', [
                        'document_type' => $documentType
                    ]);
                    $result = $this->processSPK($parsedData, $dokumentasi, $jenisSpk ?? 'survey', $uploadId);
                    DB::commit();
                    return $result;
            }
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process JSON to database', [
                'upload_id' => $uploadId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    // ============================================
    // PROCESS SPK (updated to accept direct parameters)
    // ============================================
    private function processSPK(array $parsedData, array $dokumentasi, string $jenisSpk, int $uploadId)
    {
        Log::info('📄 Processing SPK document', [
            'upload_id' => $uploadId,
            'jenis_spk' => $jenisSpk
        ]);
        
        // 1. Insert/Update JARINGAN
        $noJaringan = $this->processJaringan(
            $parsedData['jaringan'] ?? [],
            $parsedData['pelanggan'] ?? []
        );
        
        // 2. Insert SPK (parent)
        $idSpk = $this->createSpk(
            $parsedData['spk'] ?? [],
            $noJaringan,
            $jenisSpk,
            $uploadId
        );
        
        // 3. Insert data detail
        $this->safeProcess('Pelaksanaan', function() use ($parsedData, $idSpk) {
            $this->processPelaksanaan($parsedData['pelaksanaan'] ?? [], $idSpk);
        });
        
        $this->safeProcess('ExecutionInfo', function() use ($parsedData, $idSpk) {
            $vendorData = $parsedData['vendor'] ?? $parsedData['pekerja_cabut'] ?? [];
            $this->processExecutionInfo($vendorData, $idSpk);
        });
        
        $this->safeProcess('InformasiGedung', function() use ($parsedData, $idSpk) {
            $this->processInformasiGedung($parsedData['informasi_gedung'] ?? [], $idSpk);
        });
        
        $this->safeProcess('SarpenRuangServer', function() use ($parsedData, $idSpk) {
            $this->processSarpenRuangServer($parsedData['sarpen_ruang_server'] ?? [], $idSpk);
        });
        
        $this->safeProcess('LokasiAntena', function() use ($parsedData, $idSpk) {
            $this->processLokasiAntena($parsedData['lokasi_antena'] ?? [], $idSpk);
        });
        
        $this->safeProcess('PerizinanBiayaGedung', function() use ($parsedData, $idSpk) {
            $this->processPerizinanBiayaGedung($parsedData['perizinan_biaya_gedung'] ?? [], $idSpk);
        });
        
        $this->safeProcess('PenempatanPerangkat', function() use ($parsedData, $idSpk) {
            $this->processPenempatanPerangkat($parsedData['penempatan_perangkat'] ?? [], $idSpk);
        });
        
        $this->safeProcess('PerizinanBiayaKawasan', function() use ($parsedData, $idSpk) {
            $this->processPerizinanBiayaKawasan($parsedData['perizinan_biaya_kawasan'] ?? [], $idSpk);
        });
        
        $this->safeProcess('KawasanUmum', function() use ($parsedData, $idSpk) {
            $this->processKawasanUmum($parsedData['kawasan_umum'] ?? [], $idSpk);
        });
        
        $this->safeProcess('DataSplitter', function() use ($parsedData, $idSpk) {
            $this->processDataSplitter($parsedData['data_splitter'] ?? [], $idSpk);
        });
        
        $this->safeProcess('HhEksisting', function() use ($parsedData, $idSpk) {
            $this->processHhEksisting($parsedData['data_hh_eksisting'] ?? [], $idSpk);
        });
        
        $this->safeProcess('HhBaru', function() use ($parsedData, $idSpk) {
            $this->processHhBaru($parsedData['data_hh_baru'] ?? [], $idSpk);
        });
        
        $this->safeProcess('ListItem', function() use ($parsedData, $idSpk) {
            $this->processListItem($parsedData['list_item'] ?? [], $idSpk);
        });
        
        $this->safeProcess('DokumentasiFoto', function() use ($dokumentasi, $idSpk) {
            $this->processDokumentasiFoto($dokumentasi, $idSpk);
        });
        
        $this->safeProcess('BeritaAcara', function() use ($parsedData, $idSpk) {
            $this->processBeritaAcara($parsedData['berita_acara'] ?? [], $idSpk);
        });
        
        event(new \App\Events\SPKDataSaved($idSpk, $noJaringan));
        
        Log::info(' SPK successfully processed to database', [
            'upload_id' => $uploadId,
            'id_spk' => $idSpk,
            'no_jaringan' => $noJaringan
        ]);
        
        return [
            'success' => true,
            'id_spk' => $idSpk,
            'no_jaringan' => $noJaringan
        ];
    }
    
    // ============================================
    // PROCESS FORM CHECKLIST WIRELINE (updated)
    // ============================================
    private function processFormChecklistWireline(array $parsedData, int $uploadId)
    {
        Log::info('Processing Form Checklist Wireline', [
            'upload_id' => $uploadId
        ]);
        
        //  FIX: Validasi parsed data
        if (empty($parsedData)) {
            throw new Exception('Parsed data is empty. Document parsing failed completely.');
        }
        
        //  FIX: Check apakah minimal data_remote ada
        if (!isset($parsedData['data_remote']) || empty($parsedData['data_remote'])) {
            Log::error('data_remote not found in parsed data', [
                'upload_id' => $uploadId,
                'available_keys' => array_keys($parsedData),
            ]);
            
            throw new Exception('Failed to extract basic information from document. The document may be heavily distorted or OCR quality is too low.');
        }
        
        //  Ambil no_jaringan dengan fallback
        $noJaringan = $parsedData['data_remote']['no_jaringan'] 
            ?? $parsedData['data_remote']['nomor_jaringan']
            ?? $parsedData['data_remote']['Nomor Jaringan']  // Case variation
            ?? null;
        
        if (!$noJaringan) {
            //  Debug: Log semua keys di data_remote
            Log::error('no_jaringan not found in data_remote', [
                'upload_id' => $uploadId,
                'data_remote_keys' => array_keys($parsedData['data_remote']),
                'data_remote_values' => $parsedData['data_remote'],
            ]);
            
            throw new Exception('no_jaringan or nomor_jaringan is required for Form Checklist Wireline but not found in extracted data. Please check if the PDF is readable.');
        }
        
        // Rest of the code remains the same...
        $noSpk = $parsedData['data_remote']['no_spk'] ?? null;
        if (!$noSpk) {
            throw new Exception('no_spk is required for Form Checklist Wireline');
        }
        
        //  Cek apakah SPK sudah ada, jika belum create
        $spk = Spk::where('no_spk', $noSpk)->first();
        
        if (!$spk) {
            Log::info('SPK not found, creating new SPK for FCW', [
                'no_spk' => $noSpk,
                'no_jaringan' => $noJaringan
            ]);
            
            // Create JARINGAN dulu jika belum ada
            $this->ensureJaringanExists($noJaringan, $parsedData['data_remote'] ?? []);
            
            // Create SPK
            $spk = Spk::create([
                'no_spk' => $noSpk,
                'no_jaringan' => $noJaringan,
                'document_type' => 'form_checklist_wireline',
                'jenis_spk' => 'maintenance',
                'tanggal_spk' => $this->parseDate($parsedData['data_remote']['tanggal'] ?? null) ?? now(),
                'id_upload' => $uploadId,
            ]);
        }
        
        //  Call FormChecklistWirelineService
        $fcwService = new FormChecklistWirelineService();
        
        // Build jsonData structure that service expects
        $jsonDataForService = [
            'data' => [
                'parsed' => [
                    'data' => $parsedData
                ]
            ]
        ];
        
        $result = $fcwService->process($jsonDataForService, $spk->id_spk, $uploadId);
        
        Log::info(' Form Checklist Wireline successfully processed', [
            'upload_id' => $uploadId,
            'id_spk' => $spk->id_spk,
            'id_fcw' => $result['id_fcw']
        ]);
        
        return [
            'success' => true,
            'id_spk' => $spk->id_spk,
            'id_fcw' => $result['id_fcw'],
            'no_jaringan' => $noJaringan
        ];
    }
    
    // ============================================
    // PROCESS FORM CHECKLIST WIRELESS (updated)
    // ============================================
    private function processFormChecklistWireless(array $parsedData, int $uploadId)
    {
        Log::info('Processing Form Checklist Wireless', [
            'upload_id' => $uploadId
        ]);
        
        //  Ambil no_jaringan dari data_remote (support both field names)
        $noJaringan = $parsedData['data_remote']['no_jaringan'] 
            ?? $parsedData['data_remote']['nomor_jaringan']
            ?? null;
        
        if (!$noJaringan) {
            throw new Exception('no_jaringan or nomor_jaringan is required for Form Checklist Wireless');
        }
        
        //  Ambil no_spk
        $noSpk = $parsedData['data_remote']['no_spk'] ?? null;
        if (!$noSpk) {
            throw new Exception('no_spk is required for Form Checklist Wireless');
        }
        
        //  Cek apakah SPK sudah ada, jika belum create
        $spk = Spk::where('no_spk', $noSpk)->first();
        
        if (!$spk) {
            Log::info('SPK not found, creating new SPK for FCWL', [
                'no_spk' => $noSpk,
                'no_jaringan' => $noJaringan
            ]);
            
            // Create JARINGAN dulu jika belum ada
            $this->ensureJaringanExists($noJaringan, $parsedData['data_remote'] ?? []);
            
            // Create SPK
            $spk = Spk::create([
                'no_spk' => $noSpk,
                'no_jaringan' => $noJaringan,
                'document_type' => 'form_checklist_wireless',
                'jenis_spk' => 'maintenance',
                'tanggal_spk' => $this->parseDate($parsedData['data_remote']['tanggal'] ?? null) ?? now(),
                'id_upload' => $uploadId,
            ]);
        }
        
        //  Call FormChecklistWirelessService
        $fcwlService = new FormChecklistWirelessService();
        
        // Build jsonData structure that service expects
        $jsonDataForService = [
            'data' => [
                'parsed' => [
                    'data' => $parsedData
                ]
            ]
        ];
        
        $result = $fcwlService->process($jsonDataForService, $spk->id_spk, $uploadId);
        
        Log::info(' Form Checklist Wireless successfully processed', [
            'upload_id' => $uploadId,
            'id_spk' => $spk->id_spk,
            'id_fcwl' => $result['id_fcwl']
        ]);
        
        return [
            'success' => true,
            'id_spk' => $spk->id_spk,
            'id_fcwl' => $result['id_fcwl'],
            'no_jaringan' => $noJaringan
        ];
    }
    
    // ============================================
    // HELPER: Ensure JARINGAN exists
    // ============================================
    private function ensureJaringanExists(string $noJaringan, array $dataRemote)
    {
        $jaringan = Jaringan::where('no_jaringan', $noJaringan)->first();
        
        if (!$jaringan) {
            Log::info('Creating JARINGAN for FCW/FCWL', [
                'no_jaringan' => $noJaringan
            ]);
            
            Jaringan::create([
                'no_jaringan' => $noJaringan,
                'nama_pelanggan' => $dataRemote['nama_pelanggan'] ?? null,
                'lokasi_pelanggan' => $dataRemote['alamat'] ?? null,
                'jasa' => 'Unknown', // Default value
                'media_akses' => null,
            ]);
        }
    }
    
    // ============================================
    // EXISTING METHODS (unchanged)
    // ============================================
    
    private function safeProcess(string $name, callable $callback)
    {
        try {
            $callback();
        } catch (Exception $e) {
            Log::warning("Failed to process {$name}", [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }
    
    private function processJaringan(array $jaringanData, array $pelangganData)
    {
        //  FIX: Support both no_jaringan and nomor_jaringan
        $noJaringan = $jaringanData['no_jaringan'] 
            ?? $jaringanData['nomor_jaringan']
            ?? null;
        
        if (empty($noJaringan)) {
            throw new Exception('no_jaringan or nomor_jaringan is required');
        }
        
        $jaringan = Jaringan::updateOrCreate(
            ['no_jaringan' => $noJaringan],
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
    
    private function createSpk(array $spkData, string $noJaringan, string $jenisSpk, int $uploadId)
    {
        $noSpk = $spkData['no_spk'] ?? null;
        
        if (empty($noSpk)) {
            throw new Exception('no_spk is required');
        }
        
        $spk = Spk::create([
            'no_spk' => $noSpk,
            'no_jaringan' => $noJaringan,
            'document_type' => 'spk',
            'jenis_spk' => $jenisSpk,
            'tanggal_spk' => $this->parseDate($spkData['tanggal_spk'] ?? null),
            'no_mr' => $spkData['no_mr'] ?? null,
            'no_fps' => $spkData['no_fps'] ?? null,
            'id_upload' => $uploadId,
        ]);
        
        return $spk->id_spk;
    }
    
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
    
    private function processListItem(array $items, int $idSpk)
    {
        if (empty($items)) {
            Log::info('No list_item data to process', ['id_spk' => $idSpk]);
            return;
        }
        
        Log::info('Processing list items', [
            'id_spk' => $idSpk,
            'count' => count($items)
        ]);
        
        foreach ($items as $item) {
            if (empty($item) || !is_array($item)) {
                continue;
            }
            
            if (empty($item['kode']) && empty($item['deskripsi'])) {
                continue;
            }
            
            ListItem::create([
                'id_spk' => $idSpk,
                'kode' => $item['kode'] ?? null,
                'deskripsi' => $item['deskripsi'] ?? null,
                'serial_number' => $item['serial_number'] ?? $item['serial'] ?? null,
            ]);
            
            Log::debug('List item created', [
                'id_spk' => $idSpk,
                'kode' => $item['kode'] ?? 'null',
                'deskripsi' => substr($item['deskripsi'] ?? '', 0, 50)
            ]);
        }
    }
    
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
    
    private function processSarpenRuangServer(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkSarpenRuangServer::create([
            'id_spk' => $idSpk,
            'power_line_listrik' => $data['power_line___listrik'] ?? null,
            'ketersediaan_power_outlet' => $data['ketersediaan_power_outlet_untuk_otb,_modem,_dan_router'] ?? null,
            'grounding_listrik' => $this->mapAdaTidakAda($data['grounding_listrik'] ?? null),
            'ups' => $this->mapTersediaTidak($data['ups'] ?? null),
            'ruangan_ber_ac' => $this->mapAdaTidakAda($data['ruangan_ber_ac'] ?? null),
            'suhu_ruangan_keterangan' => $data['suhu_ruangan'] ?? null,
            'lantai' => $data['1_lantai'] ?? null,
            'ruang' => $data['2_ruang'] ?? null,
            'perangkat_pelanggan' => $data['perangkat_pelanggan'] ?? null,
        ]);
    }
    
    private function processLokasiAntena(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkLokasiAntena::create([
            'id_spk' => $idSpk,
            'lokasi_antena' => $data['lokasi_antena'] ?? null,
            'detail_lokasi_antena' => $data['detail_lokasi_antena'] ?? null,
            'space_tersedia' => $data['space_tersedia'] ?? null,
            'akses_di_lokasi_perlu_alat_bantu' => $data['akses_di_lokasi_perlu_alat_bantu'] ?? null,
            'penangkal_petir' => $this->mapAdaTidakAda($data['penangkal_petir'] ?? null),
            'tinggi_penangkal_petir' => $data['tinggi_penangkal_petir'] ?? null,
            'jarak_ke_lokasi_antena' => $data['jarak_ke_lokasi_antena'] ?? null,
            'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
            'tower_pole' => $this->mapAdaTidakAda($data['tower___pole'] ?? null),
            'pemilik_tower_pole' => $data['pemilik_tower___pole'] ?? null,
        ]);
    }
    
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
    
    private function processPenempatanPerangkat(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkPenempatanPerangkat::create([
            'id_spk' => $idSpk,
            'lokasi_penempatan_modem_dan_router' => $data['lokasi_penempatan_modem_dan_router'] ?? null,
            'kesiapan_ruang_server' => $this->mapSiapTidakSiap($data['kesiapan_ruang_server'] ?? null),
            'ketersedian_rak_server' => $this->mapAdaTidakAda($data['ketersedian_rak_server'] ?? null),
            'space_modem_dan_router' => $this->mapAdaTidakAda($data['space_modem_dan_router'] ?? null),
            'diizinkan_foto_ruang_server_pelanggan' => $this->mapYaTidak($data['diizinkan_foto_ruang_server_pelanggan'] ?? null),
        ]);
    }
    
    private function processPerizinanBiayaKawasan(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkPerizinanBiayaKawasan::create([
            'id_spk' => $idSpk,
            'melewati_kawasan_private' => $this->mapYaTidak($data['melewati_kawasan_private'] ?? null) ?? 'tidak',
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
    
    private function processKawasanUmum(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        SpkKawasanUmum::create([
            'id_spk' => $idSpk,
            'nama_kawasan_umum_pu_yang_dilewati' => $data['nama_kawasan_umum___pu_yang_dilewati'] ?? null,
            'panjang_jalur_outdoor_di_kawasan_umum' => $data['panjang_jalur_outdoor_di_kawasan_umum'] ?? null,
        ]);
    }

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

    private function processHhEksisting(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        foreach ($data as $key => $hh) {
            if (empty($hh) || !is_array($hh)) continue;
            
            preg_match('/hh_(\d+)/', $key, $matches);
            $nomorHh = $matches[1] ?? 1;
            
            $hasData = false;
            foreach ($hh as $value) {
                if (!empty($value) && $value !== null) {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData) continue;
            
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

    private function processHhBaru(array $data, int $idSpk)
    {
        if (empty($data)) return;
        
        foreach ($data as $key => $hh) {
            if (empty($hh) || !is_array($hh)) continue;
            
            preg_match('/hh_(\d+)/', $key, $matches);
            $nomorHh = $matches[1] ?? 1;
            
            $hasData = false;
            foreach ($hh as $value) {
                if (!empty($value) && $value !== null) {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData) continue;
            
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

    private function processDokumentasiFoto(array $dokumentasiArray, int $idSpk)
    {
        if (empty($dokumentasiArray)) return;
        
        foreach ($dokumentasiArray as $index => $foto) {
            if (empty($foto['patch_foto'])) continue;
            
            $pathFoto = str_replace('\\', '/', $foto['patch_foto']);
            $kategori = $this->mapKategoriFoto($foto['jenis'] ?? '');
            
            DokumentasiFoto::create([
                'id_spk' => $idSpk,
                'kategori_foto' => $kategori,
                'path_foto' => $pathFoto,
                'urutan' => $index + 1,
                'keterangan' => $foto['jenis'] ?? null,
            ]);
        }
    }

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
        if (empty($dateString) || $dateString === '-' || $dateString === 'null') return null;
        
        try {
            $dateString = trim($dateString);
            
            $formats = ['d/M/Y', 'd-M-Y', 'd/m/Y', 'd-m-Y', 'Y-m-d', 'd/M/Y H:i', 'd-M-Y H:i'];
            
            foreach ($formats as $format) {
                try {
                    $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                    if ($date) {
                        return $date->format('Y-m-d');
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
            
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
        if (empty($dateTimeString) || $dateTimeString === '-' || $dateTimeString === 'null') return null;
        
        try {
            $dateTimeString = preg_replace('/\s+/', ' ', trim($dateTimeString));
            
            $formats = ['d/M/Y H:i', 'd-M-Y H:i', 'd/m/Y H:i', 'd-m-Y H:i'];
            
            foreach ($formats as $format) {
                try {
                    $date = \Carbon\Carbon::createFromFormat($format, $dateTimeString);
                    if ($date) {
                        return $date->format('Y-m-d H:i:s');
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
            
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
        if (empty($value) || $value === 'null') return null;
        
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
        if (empty($value) || $value === 'null') return null;
        
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
        if (empty($value) || $value === 'null') return null;
        
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
        if (empty($value) || $value === 'null') return null;
        
        $lower = strtolower(trim($value));
        if (strpos($lower, 'ya') !== false || $lower === 'y') {
            return 'ya';
        } elseif (strpos($lower, 'tidak') !== false || $lower === 'n') {
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
        if (empty($latLongString) || $latLongString === 'null') return null;
        
        $parts = explode(',', $latLongString);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }

    private function extractLongitude($latLongString)
    {
        if (empty($latLongString) || $latLongString === 'null') return null;
        $parts = explode(',', $latLongString);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    /**
     * Infer jenis_spk from document_type
     */
    private function inferJenisSpk(string $documentType): string
    {
        // Extract jenis from document_type (e.g., "spk_survey" → "survey")
        if (strpos($documentType, 'spk_') === 0) {
            return str_replace('spk_', '', $documentType);
        }
        
        return 'survey'; // default
    }
}