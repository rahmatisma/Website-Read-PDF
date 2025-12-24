<?php
// ============================================
// FILE 2: app/Services/FormChecklistWirelessService.php
// ============================================

namespace App\Services;

use App\Models\FormChecklistWireless;
use App\Models\FcwlWaktuPelaksanaan;
use App\Models\FcwlTegangan;
use App\Models\FcwlIndoorArea;
use App\Models\FcwlIndoorParameter;
use App\Models\FcwlOutdoorArea;
use App\Models\FcwlOutdoorParameter;
use App\Models\FcwlPerangkatAntenna;
use App\Models\FcwlCablingInstallation;
use App\Models\FcwlDataPerangkat;
use App\Models\FcwlGuidanceFoto;
use App\Models\FcwlLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FormChecklistWirelessService
{
    public function process(array $jsonData, int $idSpk, int $uploadId)
    {
        DB::beginTransaction();
        
        try {
            $parsedData = null;
            $dokumentasi = null;
            
            if (isset($jsonData['data']['parsed']['data'])) {
                $parsedData = $jsonData['data']['parsed']['data'];
                $dokumentasi = $jsonData['data']['dokumentasi'] ?? [];
            } elseif (isset($jsonData['parsed']['data'])) {
                $parsedData = $jsonData['parsed']['data'];
                $dokumentasi = $jsonData['dokumentasi'] ?? [];
            } else {
                throw new Exception('Invalid JSON structure: parsed.data not found');
            }
            
            Log::info('Processing Form Checklist Wireless', ['upload_id' => $uploadId, 'id_spk' => $idSpk]);
            
            $idFcwl = $this->processFormChecklistWireless($parsedData['data_remote'] ?? [], $idSpk);
            
            $this->safeProcess('WaktuPelaksanaan', fn() => $this->processWaktuPelaksanaan($parsedData['data_remote']['pelaksanaan'] ?? [], $idFcwl));
            $this->safeProcess('Tegangan', fn() => $this->processTegangan($parsedData['indoor_area_checklist']['sarana_penunjang']['pengukuran_tegangan'] ?? [], $idFcwl));
            $this->safeProcess('IndoorArea', fn() => $this->processIndoorArea($parsedData['indoor_area_checklist'] ?? [], $idFcwl));
            $this->safeProcess('OutdoorArea', fn() => $this->processOutdoorArea($parsedData['outdoor_area_checklist'] ?? [], $idFcwl));
            $this->safeProcess('PerangkatAntenna', fn() => $this->processPerangkatAntenna($parsedData['outdoor_area_checklist']['perangkat_antenna'] ?? [], $idFcwl));
            $this->safeProcess('CablingInstallation', fn() => $this->processCablingInstallation($parsedData['outdoor_area_checklist']['cabling_installation'] ?? [], $idFcwl));
            $this->safeProcess('DataPerangkat', fn() => $this->processDataPerangkat($parsedData['data_perangkat'] ?? [], $idFcwl));
            $this->safeProcess('GuidanceFoto', fn() => $this->processGuidanceFoto($dokumentasi, $idFcwl));
            $this->safeProcess('Log', fn() => $this->processLog($parsedData['log'] ?? [], $idFcwl));
            
            DB::commit();
            
            Log::info('Form Checklist Wireless successfully processed', ['upload_id' => $uploadId, 'id_fcwl' => $idFcwl]);
            
            return ['success' => true, 'id_fcwl' => $idFcwl];
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process Form Checklist Wireless', ['upload_id' => $uploadId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
    
    private function safeProcess(string $name, callable $callback)
    {
        try {
            $callback();
        } catch (Exception $e) {
            Log::warning("Failed to process {$name}", ['error' => $e->getMessage()]);
        }
    }
    
    private function processFormChecklistWireless(array $dataRemote, int $idSpk)
    {
        if (empty($dataRemote['no_spk'])) throw new Exception('no_spk is required');
        
        $fcwl = FormChecklistWireless::create([
            'id_spk' => $idSpk,
            'no_spk' => $dataRemote['no_spk'],
            'tanggal' => $this->parseDate($dataRemote['tanggal'] ?? null),
            'nama_pelanggan' => $dataRemote['nama_pelanggan'] ?? null,
            'contact_person' => $dataRemote['contact_person'] ?? null,
            'nomor_telepon' => $dataRemote['nomor_telepon'] ?? null,
            'alamat' => $dataRemote['alamat'] ?? null,
            'kota' => $dataRemote['kota'] ?? null,
            'propinsi' => $dataRemote['propinsi'] ?? null,
        ]);
        
        return $fcwl->id_fcwl;
    }
    
    private function processWaktuPelaksanaan(array $pelaksanaan, int $idFcwl)
    {
        if (empty($pelaksanaan)) return;
        
        $waktuMapping = [
            'jam_perintah' => 'perintah', 'jam_persiapan' => 'persiapan', 'jam_berangkat' => 'berangkat',
            'jam_tiba_di_lokasi' => 'tiba_lokasi', 'jam_mulai_kerja' => 'mulai_kerja', 
            'jam_selesai_kerja' => 'selesai_kerja', 'jam_pulang' => 'pulang', 'jam_tiba_di_kantor' => 'tiba_kantor',
        ];
        
        foreach ($waktuMapping as $jsonKey => $jenisWaktu) {
            if (!empty($pelaksanaan[$jsonKey])) {
                FcwlWaktuPelaksanaan::create([
                    'id_fcwl' => $idFcwl,
                    'jenis_waktu' => $jenisWaktu,
                    'waktu' => $this->parseDateTime($pelaksanaan[$jsonKey]),
                    'keterangan' => $pelaksanaan['keterangan'] ?? null,
                ]);
            }
        }
    }
    
    private function processTegangan(array $teganganArray, int $idFcwl)
    {
        if (empty($teganganArray)) return;
        
        foreach ($teganganArray as $tegangan) {
            if (empty($tegangan['v_output'])) continue;
            
            $jenisSumber = $this->mapJenisSumber($tegangan['v_output']);
            $pn = $this->parseVoltage($tegangan['p_n'] ?? null);
            $pg = $this->parseVoltage($tegangan['p_g'] ?? null);
            $ng = $this->parseVoltage($tegangan['n_g'] ?? null);
            
            if (empty($pn) && empty($pg) && empty($ng)) continue;
            
            FcwlTegangan::create([
                'id_fcwl' => $idFcwl,
                'jenis_sumber' => $jenisSumber,
                'p_n' => $pn,
                'p_g' => $pg,
                'n_g' => $ng,
            ]);
        }
    }
    
    private function processIndoorArea(array $indoorData, int $idFcwl)
    {
        if (empty($indoorData)) return;
        
        $saranaPenunjang = $indoorData['sarana_penunjang'] ?? [];
        $perangkatModem = $indoorData['perangkat_modem'] ?? [];
        $perangkatCpe = $indoorData['perangkat_cpe'] ?? [];
        
        $indoor = FcwlIndoorArea::create([
            'id_fcwl' => $idFcwl,
            'merk_ups' => $saranaPenunjang['merk_ups'] ?? null,
            'kapasitas_ups' => $saranaPenunjang['kapasitas_ups'] ?? null,
            'jenis_ups' => $saranaPenunjang['jenis_ups'] ?? null,
            'ruangan_bebas_debu' => $this->mapYaTidak($this->extractFromParameter($saranaPenunjang['parameter_kualitas'] ?? [], 'Ruangan Bebas Debu')),
            'suhu_ruangan' => $this->parseDecimal($this->extractFromParameter($saranaPenunjang['parameter_kualitas'] ?? [], 'Suhu Ruangan')),
            'terpasang_ground_bar' => $this->mapYaTidak($this->extractFromParameter($saranaPenunjang['parameter_kualitas'] ?? [], 'Terpasang ground bar')),
            'catuan_input_modem' => $perangkatModem['catatan_input_modem'] ?? null,
            'v_input_modem_p_n' => $this->parseVoltage($this->extractFromParameter($perangkatModem['parameter_kualitas'] ?? [], 'V. Input Modem (P-N)')),
            'v_input_modem_n_g' => $this->parseVoltage($this->extractFromParameter($perangkatModem['parameter_kualitas'] ?? [], 'V. Input Modem (N-G)')),
            'bertumpuk' => $this->mapYaTidak($perangkatModem['bertumpuk'] ?? null),
            'lokasi_ruang' => $perangkatModem['lokasi_ruang_lantai_rack'] ?? null,
            'suhu_casing_modem' => $this->parseDecimal($this->extractFromParameter($perangkatModem['parameter_kualitas'] ?? [], 'Suhu casing modem')),
            'catuan_input_terbounding' => $this->mapYaTidak($this->extractFromParameter($perangkatModem['parameter_kualitas'] ?? [], 'Catuan input terbounding')),
            'splicing_konektor_kabel' => $this->extractFromParameter($perangkatModem['parameter_kualitas'] ?? [], 'Splicing konektor'),
            'pemilik_perangkat_cpe' => $perangkatCpe['pemilik_perangkat_cpe'] ?? null,
            'jenis_perangkat_cpe' => $perangkatCpe['jenis_perangkat_cpe'] ?? null,
        ]);
        
        $this->processIndoorParameters($saranaPenunjang['parameter_kualitas'] ?? [], $indoor->id_indoor, 'sarana_penunjang');
        $this->processIndoorParameters($perangkatModem['parameter_kualitas'] ?? [], $indoor->id_indoor, 'perangkat_modem');
        $this->processIndoorParameters($perangkatCpe['parameter_kualitas'] ?? [], $indoor->id_indoor, 'perangkat_cpe');
    }
    
    private function processIndoorParameters(array $parameters, int $idIndoor, string $kategori)
    {
        if (empty($parameters)) return;
        
        $urutan = 1;
        foreach ($parameters as $param) {
            if (empty($param['quality_parameter'])) continue;
            
            FcwlIndoorParameter::create([
                'id_indoor' => $idIndoor,
                'kategori' => $kategori,
                'quality_parameter' => $param['quality_parameter'],
                'standard' => $param['standard'] ?? null,
                'existing' => $param['existing'] ?? null,
                'urutan' => $urutan++,
            ]);
        }
    }
    
    private function processOutdoorArea(array $outdoorData, int $idFcwl)
    {
        if (empty($outdoorData)) return;
        
        $site = $outdoorData['site'] ?? [];
        $saranaPenunjang = $outdoorData['sarana_penunjang'] ?? [];
        
        $outdoor = FcwlOutdoorArea::create([
            'id_fcwl' => $idFcwl,
            'bs_catuan_sektor' => $site['bs_catuan_sektor'] ?? null,
            'los_ke_bs_catuan' => $this->mapYaTidak($this->extractFromParameter($site['quality_parameter'] ?? [], 'LOS ke BS Catuan')),
            'jarak_udara' => $site['jarak_udara_heading'] ?? null,
            'heading' => null,
            'latitude' => $this->parseDecimal($site['latitude'] ?? null),
            'longitude' => $this->parseDecimal($site['longitude'] ?? null),
            'potential_obstacle' => $site['potential_obstacle'] ?? null,
            'type_mounting' => $saranaPenunjang['type_mounting'] ?? null,
            'mounting_tidak_goyang' => $this->mapYaTidak($this->extractFromParameter($saranaPenunjang['quality_parameter'] ?? [], 'Mounting tidak goyang')),
            'center_of_gravity' => $this->extractFromParameter($saranaPenunjang['quality_parameter'] ?? [], 'Center of gravity'),
            'disekitar_mounting_ada_penangkal_petir' => $this->mapYaTidak($this->extractFromParameter($saranaPenunjang['quality_parameter'] ?? [], 'Disekitar mounting')),
            'sudut_mounting_terhadap_penangkal_petir' => $this->extractFromParameter($saranaPenunjang['quality_parameter'] ?? [], 'Sudut mounting'),
            'tinggi_mounting' => $saranaPenunjang['tinggi_mounting'] ?? null,
            'type_penangkal_petir' => $saranaPenunjang['type_penangkal_petir'] ?? null,
        ]);
        
        $this->processOutdoorParameters($site['quality_parameter'] ?? [], $outdoor->id_outdoor, 'site');
        $this->processOutdoorParameters($saranaPenunjang['quality_parameter'] ?? [], $outdoor->id_outdoor, 'sarana_penunjang');
        $this->processOutdoorParameters($outdoorData['perangkat_antenna']['quality_parameter'] ?? [], $outdoor->id_outdoor, 'perangkat_antenna');
        $this->processOutdoorParameters($outdoorData['cabling_installation']['quality_parameter'] ?? [], $outdoor->id_outdoor, 'cabling_installation');
    }
    
    private function processOutdoorParameters(array $parameters, int $idOutdoor, string $kategori)
    {
        if (empty($parameters)) return;
        
        $urutan = 1;
        foreach ($parameters as $param) {
            if (empty($param['parameter'])) continue;
            
            FcwlOutdoorParameter::create([
                'id_outdoor' => $idOutdoor,
                'kategori' => $kategori,
                'parameter' => $param['parameter'],
                'standard' => $param['standard'] ?? null,
                'existing' => $param['existing'] ?? null,
                'urutan' => $urutan++,
            ]);
        }
    }
    
    private function processPerangkatAntenna(array $antennaData, int $idFcwl)
    {
        if (empty($antennaData)) return;
        
        FcwlPerangkatAntenna::create([
            'id_fcwl' => $idFcwl,
            'polarisasi' => $antennaData['polarisasi'] ?? null,
            'altitude' => $antennaData['altitude'] ?? null,
            'lokasi' => $antennaData['lokasi'] ?? null,
            'antenna_terbounding_dengan_ground' => $this->mapYaTidak($this->extractFromParameter($antennaData['quality_parameter'] ?? [], 'Antenna terbounding')),
            'posisi_antena_sejajar' => $this->mapYaTidak($this->extractFromParameter($antennaData['quality_parameter'] ?? [], 'Posisi antena sejajar')),
        ]);
    }
    
    private function processCablingInstallation(array $cablingData, int $idFcwl)
    {
        if (empty($cablingData)) return;
        
        FcwlCablingInstallation::create([
            'id_fcwl' => $idFcwl,
            'type_kabel_ifl' => $cablingData['type_kabel_ifl'] ?? null,
            'panjang_kabel_ifl' => $cablingData['panjang_kabel_ifl'] ?? null,
            'tahanan_short_kabel_ifl' => $cablingData['tahanan_short_kabel_ifl'] ?? null,
            'terpasang_arrestor' => $this->mapYaTidak($this->extractFromParameter($cablingData['quality_parameter'] ?? [], 'Terpasang arrestor')),
            'splicing_konektor_kabel_ifl' => $this->extractFromParameter($cablingData['quality_parameter'] ?? [], 'Splicing konektor'),
        ]);
    }
    
    private function processDataPerangkat(array $dataPerangkat, int $idFcwl)
    {
        if (empty($dataPerangkat)) return;
        
        $kategoriMapping = ['existing' => 'existing', 'tidak_terpakai' => 'tidak_terpakai', 'cabut' => 'cabut', 'pengganti_atau_pasang_baru' => 'pengganti_pasang_baru'];
        
        foreach ($kategoriMapping as $jsonKey => $kategori) {
            if (!isset($dataPerangkat[$jsonKey]) || !is_array($dataPerangkat[$jsonKey])) continue;
            
            foreach ($dataPerangkat[$jsonKey] as $perangkat) {
                if (empty($perangkat['nama_barang'])) continue;
                
                FcwlDataPerangkat::create([
                    'id_fcwl' => $idFcwl,
                    'kategori' => $kategori,
                    'nama_barang' => $perangkat['nama_barang'],
                    'no_reg' => $perangkat['no_reg'] ?? null,
                    'serial_number' => $perangkat['sn'] ?? $perangkat['serial_number'] ?? null,
                ]);
            }
        }
    }
    
    private function processGuidanceFoto(array $dokumentasiArray, int $idFcwl)
    {
        if (empty($dokumentasiArray)) return;
        
        foreach ($dokumentasiArray as $index => $foto) {
            if (empty($foto['patch_foto'])) continue;
            
            FcwlGuidanceFoto::create([
                'id_fcwl' => $idFcwl,
                'jenis_foto' => $this->mapJenisFoto($foto['jenis'] ?? ''),
                'path_foto' => str_replace('\\', '/', $foto['patch_foto']),
                'urutan' => $index + 1,
            ]);
        }
    }
    
    private function processLog(array $logArray, int $idFcwl)
    {
        if (empty($logArray)) return;
        
        foreach ($logArray as $log) {
            if (empty($log['date_time']) && empty($log['info'])) continue;
            
            FcwlLog::create([
                'id_fcwl' => $idFcwl,
                'date_time' => $this->parseDateTime($log['date_time'] ?? null),
                'info' => $log['info'] ?? null,
                'photo' => isset($log['photo']) ? str_replace('\\', '/', $log['photo']) : null,
            ]);
        }
    }
    
    // Helper methods
    private function parseDate($d) { if (empty($d) || $d === '-') return null; try { return \Carbon\Carbon::parse($d)->format('Y-m-d'); } catch (Exception $e) { return null; } }
    private function parseDateTime($d) { if (empty($d) || $d === '-') return null; try { return \Carbon\Carbon::parse($d)->format('Y-m-d H:i:s'); } catch (Exception $e) { return null; } }
    private function parseDecimal($v) { if (empty($v)) return null; $n = preg_replace('/[^0-9.-]/', '', $v); return !empty($n) ? (float) $n : null; }
    private function parseVoltage($v) { return $this->parseDecimal($v); }
    private function mapYaTidak($v) { if (empty($v)) return null; $l = strtolower(trim($v)); if (strpos($l, 'ya') !== false) return 'ya'; if (strpos($l, 'tidak') !== false) return 'tidak'; return null; }
    private function mapJenisSumber($v) { $l = strtolower($v); if (strpos($l, 'pln') !== false) return 'pln'; if (strpos($l, 'ups') !== false) return 'ups'; if (strpos($l, 'it') !== false) return 'it'; if (strpos($l, 'generator') !== false) return 'generator'; return 'pln'; }
    private function mapJenisFoto($j) { $j = strtolower($j); if (strpos($j, 'guidance') !== false || strpos($j, 'log') !== false) return 'guidance_umum'; if (strpos($j, 'antenna') !== false) return 'antenna_installation'; if (strpos($j, 'mounting') !== false) return 'outdoor_mounting'; return 'guidance_umum'; }
    private function extractFromParameter(array $params, string $keyword) { foreach ($params as $p) { $name = $p['quality_parameter'] ?? $p['parameter'] ?? ''; if (stripos($name, $keyword) !== false) return $p['existing'] ?? null; } return null; }
}