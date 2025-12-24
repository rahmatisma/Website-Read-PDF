<?php
// ============================================
// FILE 1: app/Services/FormChecklistWirelineService.php
// ============================================

namespace App\Services;

use App\Models\FormChecklistWireline;
use App\Models\FcwWaktuPelaksanaan;
use App\Models\FcwTegangan;
use App\Models\FcwChecklistItem;
use App\Models\FcwDataPerangkat;
use App\Models\FcwGuidanceFoto;
use App\Models\FcwLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FormChecklistWirelineService
{
    public function process(array $jsonData, int $idSpk, int $uploadId)
    {
        DB::beginTransaction();
        
        try {
            $parsedData = null;
            $dokumentasi = null;
            
            // Cek struktur JSON
            if (isset($jsonData['data']['parsed']['data'])) {
                $parsedData = $jsonData['data']['parsed']['data'];
                $dokumentasi = $jsonData['data']['dokumentasi'] ?? [];
            } elseif (isset($jsonData['parsed']['data'])) {
                $parsedData = $jsonData['parsed']['data'];
                $dokumentasi = $jsonData['dokumentasi'] ?? [];
            } else {
                throw new Exception('Invalid JSON structure: parsed.data not found');
            }
            
            Log::info('Processing Form Checklist Wireline', [
                'upload_id' => $uploadId,
                'id_spk' => $idSpk
            ]);
            
            // 1. Insert Form Checklist Wireline (Parent)
            $idFcw = $this->processFormChecklistWireline(
                $parsedData['data_remote'] ?? [],
                $parsedData['global_checklist'] ?? [],
                $idSpk
            );
            
            // 2. Insert detail data
            $this->safeProcess('WaktuPelaksanaan', function() use ($parsedData, $idFcw) {
                $this->processWaktuPelaksanaan($parsedData['data_remote']['pelaksanaan'] ?? [], $idFcw);
            });
            
            $this->safeProcess('Tegangan', function() use ($parsedData, $idFcw) {
                $this->processTegangan($parsedData['global_checklist']['electrical']['output_tegangan_mengacu_modem'] ?? [], $idFcw);
            });
            
            $this->safeProcess('IndoorAreaChecklist', function() use ($parsedData, $idFcw) {
                $this->processIndoorAreaChecklist($parsedData['indoor_area_checklist'] ?? [], $idFcw);
            });
            
            $this->safeProcess('LineChecklist', function() use ($parsedData, $idFcw) {
                $this->processLineChecklist($parsedData['line_checklist'] ?? [], $idFcw);
            });
            
            $this->safeProcess('DataPerangkat', function() use ($parsedData, $idFcw) {
                $this->processDataPerangkat($parsedData['data_perangkat'] ?? [], $idFcw);
            });
            
            $this->safeProcess('GuidanceFoto', function() use ($dokumentasi, $idFcw) {
                $this->processGuidanceFoto($dokumentasi, $idFcw);
            });
            
            $this->safeProcess('Log', function() use ($parsedData, $idFcw) {
                $this->processLog($parsedData['log'] ?? [], $idFcw);
            });
            
            DB::commit();
            
            Log::info('Form Checklist Wireline successfully processed', [
                'upload_id' => $uploadId,
                'id_spk' => $idSpk,
                'id_fcw' => $idFcw
            ]);
            
            return [
                'success' => true,
                'id_fcw' => $idFcw
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process Form Checklist Wireline', [
                'upload_id' => $uploadId,
                'id_spk' => $idSpk,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            throw $e;
        }
    }
    
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
    
    private function processFormChecklistWireline(array $dataRemote, array $globalChecklist, int $idSpk)
    {
        $noSpk = $dataRemote['no_spk'] ?? null;
        
        if (empty($noSpk)) {
            throw new Exception('no_spk is required');
        }
        
        $dataLokasi = $globalChecklist['data_lokasi'] ?? [];
        $environment = $globalChecklist['environment'] ?? [];
        $electrical = $globalChecklist['electrical'] ?? [];
        
        $fcw = FormChecklistWireline::create([
            'id_spk' => $idSpk,
            'no_spk' => $noSpk,
            'tanggal' => $this->parseDate($dataRemote['tanggal'] ?? null),
            
            // Data Remote
            'nama_pelanggan' => $dataRemote['nama_pelanggan'] ?? null,
            'contact_person' => $dataRemote['contact_person'] ?? null,
            'nomor_telepon' => $dataRemote['nomor_telepon'] ?? null,
            'alamat' => $dataRemote['alamat'] ?? null,
            
            // Global Checklist - Data Lokasi
            'kota' => $dataRemote['kota'] ?? null,
            'propinsi' => $dataRemote['propinsi'] ?? null,
            'latitude' => $this->parseDecimal($dataLokasi['latitude'] ?? null),
            'longitude' => $this->parseDecimal($dataLokasi['longitude'] ?? null),
            'posisi_modem_di_lt' => $dataLokasi['posisi_modem_di_lt'] ?? null,
            'ruang' => $dataLokasi['ruang'] ?? null,
            
            // Global Checklist - Environment
            'grounding_bar_terkoneksi_ke' => $electrical['grounding_bar_terkoneksi_ke'] ?? null,
            'ac_pendingin_ruangan' => $this->mapAdaTidakAda($environment['ac_pendingin_ruangan'] ?? null),
            'suhu_ruangan_perangkat' => $this->parseDecimal($this->extractNumber($environment['suhu_ruangan_perangkat'] ?? null)),
        ]);
        
        return $fcw->id_fcw;
    }
    
    private function processWaktuPelaksanaan(array $pelaksanaan, int $idFcw)
    {
        if (empty($pelaksanaan)) return;
        
        $waktuMapping = [
            'jam_perintah' => 'perintah',
            'jam_persiapan' => 'persiapan',
            'jam_berangkat' => 'berangkat',
            'jam_tiba_di_lokasi' => 'tiba_lokasi',
            'jam_mulai_kerja' => 'mulai_kerja',
            'jam_selesai_kerja' => 'selesai_kerja',
            'jam_pulang' => 'pulang',
            'jam_tiba_di_kantor' => 'tiba_kantor',
        ];
        
        foreach ($waktuMapping as $jsonKey => $jenisWaktu) {
            if (!empty($pelaksanaan[$jsonKey])) {
                FcwWaktuPelaksanaan::create([
                    'id_fcw' => $idFcw,
                    'jenis_waktu' => $jenisWaktu,
                    'waktu' => $this->parseDateTime($pelaksanaan[$jsonKey]),
                    'keterangan' => $pelaksanaan['keterangan'] ?? null,
                ]);
            }
        }
    }
    
    private function processTegangan(array $teganganData, int $idFcw)
    {
        if (empty($teganganData)) return;
        
        $sumberMapping = [
            'pln' => 'pln',
            'ups' => 'ups',
            'it' => 'it',
            'generator' => 'generator',
        ];
        
        foreach ($sumberMapping as $jsonKey => $jenisSumber) {
            $pn = $teganganData['p_n'][$jsonKey] ?? null;
            $pg = $teganganData['p_g'][$jsonKey] ?? null;
            $ng = $teganganData['n_g'][$jsonKey] ?? null;
            
            if (empty($pn) && empty($pg) && empty($ng)) continue;
            
            FcwTegangan::create([
                'id_fcw' => $idFcw,
                'jenis_sumber' => $jenisSumber,
                'p_n' => $this->parseVoltage($pn),
                'p_g' => $this->parseVoltage($pg),
                'n_g' => $this->parseVoltage($ng),
            ]);
        }
    }
    
    private function processIndoorAreaChecklist(array $indoorData, int $idFcw)
    {
        if (empty($indoorData)) return;
        
        $urutan = 1;
        
        // 1. Indikator Modem
        if (isset($indoorData['indikator_modem'])) {
            foreach ($indoorData['indikator_modem'] as $checkPoint => $values) {
                if (!is_array($values)) continue;
                
                FcwChecklistItem::create([
                    'id_fcw' => $idFcw,
                    'kategori' => 'indikator_modem',
                    'check_point' => $this->formatCheckPoint($checkPoint),
                    'standard' => $values['standard'] ?? null,
                    'nms_engineer' => $values['nms_engineer'] ?? null,
                    'on_site_teknisi' => $values['on_site_teknisi'] ?? null,
                    'existing' => $values['existing'] ?? null,
                    'perbaikan' => $values['perbaikan'] ?? null,
                    'hasil_akhir' => $values['hasil_akhir'] ?? null,
                    'urutan' => $urutan++,
                ]);
            }
        }
        
        // 2. Merek
        if (isset($indoorData['merek'])) {
            foreach ($indoorData['merek'] as $checkPoint => $values) {
                if ($checkPoint === 'front_panel_display' && is_array($values)) {
                    foreach ($values as $subCheckPoint => $subValues) {
                        if (!is_array($subValues)) continue;
                        
                        FcwChecklistItem::create([
                            'id_fcw' => $idFcw,
                            'kategori' => 'merek',
                            'check_point' => $this->formatCheckPoint($subCheckPoint),
                            'standard' => $subValues['standard'] ?? null,
                            'nms_engineer' => $subValues['nms_engineer'] ?? null,
                            'on_site_teknisi' => $subValues['on_site_teknisi'] ?? null,
                            'existing' => $subValues['existing'] ?? null,
                            'perbaikan' => $subValues['perbaikan'] ?? null,
                            'hasil_akhir' => $subValues['hasil_akhir'] ?? null,
                            'urutan' => $urutan++,
                        ]);
                    }
                } elseif (is_array($values)) {
                    FcwChecklistItem::create([
                        'id_fcw' => $idFcw,
                        'kategori' => 'merek',
                        'check_point' => $this->formatCheckPoint($checkPoint),
                        'standard' => $values['standard'] ?? null,
                        'nms_engineer' => $values['nms_engineer'] ?? null,
                        'on_site_teknisi' => $values['on_site_teknisi'] ?? null,
                        'existing' => $values['existing'] ?? null,
                        'perbaikan' => $values['perbaikan'] ?? null,
                        'hasil_akhir' => $values['hasil_akhir'] ?? null,
                        'urutan' => $urutan++,
                    ]);
                }
            }
        }
        
        // 3-5. Process other categories similarly
        $categories = [
            'modem_fo' => 'modem_fo',
            'lc_signal_quality_checked_by_kop' => 'lc_signal_kop',
            'lc_signal_quality_checked_by_avo_meter' => 'lc_signal_avo',
        ];
        
        foreach ($categories as $jsonKey => $kategori) {
            if (!isset($indoorData[$jsonKey])) continue;
            
            foreach ($indoorData[$jsonKey] as $checkPoint => $values) {
                if (!is_array($values)) continue;
                
                FcwChecklistItem::create([
                    'id_fcw' => $idFcw,
                    'kategori' => $kategori,
                    'check_point' => $this->formatCheckPoint($checkPoint),
                    'standard' => $values['standard'] ?? null,
                    'nms_engineer' => $values['nms_engineer'] ?? null,
                    'on_site_teknisi' => $values['on_site_teknisi'] ?? null,
                    'existing' => $values['existing'] ?? null,
                    'perbaikan' => $values['perbaikan'] ?? null,
                    'hasil_akhir' => $values['hasil_akhir'] ?? null,
                    'urutan' => $urutan++,
                ]);
            }
        }
    }
    
    private function processLineChecklist(array $lineData, int $idFcw)
    {
        if (empty($lineData)) return;
        
        $kategoriMapping = [
            'site_area' => 'site_area',
            'hrb_r_lintas' => 'hrb_r_lintas',
            'line_fo' => 'line_fo',
            'tes_konektivitas' => 'tes_konektivitas',
        ];
        
        foreach ($kategoriMapping as $jsonKey => $kategori) {
            if (!isset($lineData[$jsonKey]['parameter_kualitas'])) continue;
            
            $urutan = 1;
            foreach ($lineData[$jsonKey]['parameter_kualitas'] as $item) {
                if (empty($item['line_checklist'])) continue;
                
                FcwChecklistItem::create([
                    'id_fcw' => $idFcw,
                    'kategori' => $kategori,
                    'check_point' => $item['line_checklist'],
                    'standard' => $item['standard'] ?? null,
                    'nms_engineer' => null,
                    'on_site_teknisi' => null,
                    'existing' => $item['existing'] ?? null,
                    'perbaikan' => $item['perbaikan'] ?? null,
                    'hasil_akhir' => $item['hasil_akhir'] ?? null,
                    'urutan' => $urutan++,
                ]);
            }
        }
    }
    
    private function processDataPerangkat(array $dataPerangkat, int $idFcw)
    {
        if (empty($dataPerangkat)) return;
        
        $kategoriMapping = [
            'existing' => 'existing',
            'tidak_terpakai' => 'tidak_terpakai',
            'cabut' => 'cabut',
            'pengganti_atau_pasang_baru' => 'pengganti_pasang_baru',
        ];
        
        foreach ($kategoriMapping as $jsonKey => $kategori) {
            if (!isset($dataPerangkat[$jsonKey]) || !is_array($dataPerangkat[$jsonKey])) {
                continue;
            }
            
            foreach ($dataPerangkat[$jsonKey] as $perangkat) {
                if (empty($perangkat['nama_barang'])) continue;
                
                FcwDataPerangkat::create([
                    'id_fcw' => $idFcw,
                    'kategori' => $kategori,
                    'nama_barang' => $perangkat['nama_barang'],
                    'no_reg' => $perangkat['no_reg'] ?? null,
                    'serial_number' => $perangkat['sn'] ?? $perangkat['serial_number'] ?? null,
                ]);
            }
        }
    }
    
    private function processGuidanceFoto(array $dokumentasiArray, int $idFcw)
    {
        if (empty($dokumentasiArray)) return;
        
        foreach ($dokumentasiArray as $index => $foto) {
            if (empty($foto['patch_foto'])) continue;
            
            $pathFoto = str_replace('\\', '/', $foto['patch_foto']);
            $jenisFoto = $this->mapJenisFoto($foto['jenis'] ?? '');
            
            FcwGuidanceFoto::create([
                'id_fcw' => $idFcw,
                'jenis_foto' => $jenisFoto,
                'path_foto' => $pathFoto,
                'urutan' => $index + 1,
            ]);
        }
    }
    
    private function processLog(array $logArray, int $idFcw)
    {
        if (empty($logArray)) return;
        
        foreach ($logArray as $log) {
            if (empty($log['date_time']) && empty($log['info'])) continue;
            
            FcwLog::create([
                'id_fcw' => $idFcw,
                'date_time' => $this->parseDateTime($log['date_time'] ?? null),
                'info' => $log['info'] ?? null,
                'photo' => isset($log['photo']) ? str_replace('\\', '/', $log['photo']) : null,
            ]);
        }
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
                    if ($date) return $date->format('Y-m-d');
                } catch (Exception $e) {
                    continue;
                }
            }
            
            $date = \Carbon\Carbon::parse($dateString);
            return $date->format('Y-m-d');
            
        } catch (Exception $e) {
            Log::warning('Failed to parse date', ['input' => $dateString]);
            return null;
        }
    }
    
    private function parseDateTime($dateTimeString)
    {
        if (empty($dateTimeString) || $dateTimeString === '-' || $dateTimeString === 'null') return null;
        
        try {
            $dateTimeString = preg_replace('/\s+/', ' ', trim($dateTimeString));
            $formats = ['d/M/Y H:i', 'd-M-Y H:i', 'd/m/Y H:i', 'd-m-Y H:i', 'Y-m-d H:i:s'];
            
            foreach ($formats as $format) {
                try {
                    $date = \Carbon\Carbon::createFromFormat($format, $dateTimeString);
                    if ($date) return $date->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    continue;
                }
            }
            
            $date = \Carbon\Carbon::parse($dateTimeString);
            return $date->format('Y-m-d H:i:s');
            
        } catch (Exception $e) {
            Log::warning('Failed to parse datetime', ['input' => $dateTimeString]);
            return null;
        }
    }
    
    private function parseDecimal($value)
    {
        if (empty($value) || $value === 'null') return null;
        $number = preg_replace('/[^0-9.-]/', '', $value);
        return !empty($number) ? (float) $number : null;
    }
    
    private function extractNumber($value)
    {
        if (empty($value)) return null;
        preg_match('/[-+]?[0-9]*\.?[0-9]+/', $value, $matches);
        return $matches[0] ?? null;
    }
    
    private function parseVoltage($value)
    {
        if (empty($value) || $value === 'null') return null;
        $number = preg_replace('/[^0-9.]/', '', $value);
        return !empty($number) ? (float) $number : null;
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
    
    private function formatCheckPoint($checkPoint)
    {
        $formatted = str_replace('_', ' ', $checkPoint);
        return ucwords($formatted);
    }
    
    private function mapJenisFoto($jenis)
    {
        $jenis = strtolower($jenis);
        
        if (strpos($jenis, 'guidance') !== false) {
            return 'guidance_umum';
        } elseif (strpos($jenis, 'log') !== false) {
            return 'guidance_umum';
        } elseif (strpos($jenis, 'teknisi') !== false) {
            return 'teknisi_aktivasi';
        } elseif (strpos($jenis, 'sebelum') !== false) {
            return 'kondisi_sebelum_perbaikan';
        } elseif (strpos($jenis, 'action') !== false || strpos($jenis, 'perbaikan') !== false) {
            return 'action_perbaikan';
        } elseif (strpos($jenis, 'setelah') !== false) {
            return 'kondisi_setelah_perbaikan';
        } elseif (strpos($jenis, 'ping') !== false) {
            return 'test_ping';
        } elseif (strpos($jenis, 'listrik') !== false || strpos($jenis, 'catuan') !== false) {
            return 'catuan_listrik';
        } elseif (strpos($jenis, 'indikator') !== false) {
            return 'indikator_perangkat';
        } elseif (strpos($jenis, 'rak') !== false) {
            return 'kondisi_rak_penempatan';
        }
        
        return 'guidance_umum';
    }
}