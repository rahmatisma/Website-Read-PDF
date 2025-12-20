<?php

namespace App\Services;

use App\Models\Spk;
use App\Models\SpkEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmbeddingService
{
    protected string $flaskApiUrl;
    protected string $embeddingModel;
    protected int $embeddingDimension;
    protected int $timeout;

    public function __construct()
    {
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->embeddingModel = env('EMBEDDING_MODEL', 'nomic-embed-text');
        $this->embeddingDimension = env('EMBEDDING_DIMENSION', 384);
        $this->timeout = env('FLASK_API_TIMEOUT', 300);
    }

    /**
     * Generate COMPREHENSIVE embedding untuk SPK
     * Includes: Jaringan, Pelanggan, Teknisi, Vendor, Lokasi, Equipment, dll
     */
    public function generateSpkEmbedding(int $idSpk): ?SpkEmbedding
    {
        try {
            // 1. Ambil data SPK dengan SEMUA relasi
            $spk = Spk::with([
                'jaringan',
                'pelaksanaan',
                'executionInfo',
                'informasiGedung',
                'sarpenRuangServer',
                'lokasiAntena',
                'perizinanBiayaGedung',
                'penempatanPerangkat',
                'perizinanBiayaKawasan',
                'kawasanUmum',
                'dataSplitter',
                'hhEksisting',
                'hhBaru',
                'beritaAcara',
            ])->find($idSpk);

            if (!$spk) {
                throw new Exception("SPK with id_spk {$idSpk} not found");
            }

            // 2. Build COMPREHENSIVE content text
            $contentText = $this->buildComprehensiveSpkContent($spk);

            // 3. Generate embedding via Flask API
            $embedding = $this->callFlaskEmbedding($contentText);

            // 4. Save to database
            $spkEmbedding = SpkEmbedding::updateOrCreate(
                ['id_spk' => $idSpk],
                [
                    'no_spk' => $spk->no_spk,
                    'content_text' => $contentText,
                    'embedding' => json_encode($embedding),
                    'embedding_model' => $this->embeddingModel,
                    'embedding_dimension' => $this->embeddingDimension,
                ]
            );

            Log::info('SPK embedding generated', [
                'id_spk' => $idSpk,
                'no_spk' => $spk->no_spk,
                'content_length' => strlen($contentText),
                'embedding_dimension' => count($embedding)
            ]);

            return $spkEmbedding;

        } catch (Exception $e) {
            Log::error('Failed to generate SPK embedding', [
                'id_spk' => $idSpk,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build COMPREHENSIVE content text - SEMUA field penting untuk semantic search
     */
    private function buildComprehensiveSpkContent(Spk $spk): string
    {
        $parts = [];

        // ========================================
        // SECTION 1: SPK HEADER (Primary Info)
        // ========================================
        $parts[] = "=== INFORMASI SPK ===";
        $parts[] = "Nomor SPK: {$spk->no_spk}";
        $parts[] = "Jenis SPK: {$spk->jenis_spk}";
        $parts[] = "Tipe Dokumen: {$spk->document_type}";
        $parts[] = "Tanggal SPK: " . ($spk->tanggal_spk ?? '-');
        
        if ($spk->no_fps) {
            $parts[] = "Nomor FPS: {$spk->no_fps}";
        }
        if ($spk->no_mr) {
            $parts[] = "Nomor MR: {$spk->no_mr}";
        }

        // ========================================
        // SECTION 2: JARINGAN & PELANGGAN
        // ========================================
        if ($spk->jaringan) {
            $j = $spk->jaringan;
            $parts[] = "\n=== INFORMASI JARINGAN & PELANGGAN ===";
            $parts[] = "Nomor Jaringan: {$j->no_jaringan}";
            $parts[] = "Nama Pelanggan: {$j->nama_pelanggan}";
            $parts[] = "Lokasi Pelanggan: {$j->lokasi_pelanggan}";
            $parts[] = "Jasa: {$j->jasa}";
            
            if ($j->media_akses) {
                $parts[] = "Media Akses: {$j->media_akses}";
            }
            if ($j->kecepatan) {
                $parts[] = "Kecepatan: {$j->kecepatan}";
            }
            if ($j->manage_router) {
                $parts[] = "Managed Router: {$j->manage_router}";
            }
            if ($j->opsi_router) {
                $parts[] = "Opsi Router: {$j->opsi_router}";
            }
            if ($j->ip_lan) {
                $parts[] = "IP LAN: {$j->ip_lan}";
            }
            if ($j->kode_jaringan) {
                $parts[] = "Kode Jaringan: {$j->kode_jaringan}";
            }
            if ($j->no_fmb) {
                $parts[] = "Nomor FMB: {$j->no_fmb}";
            }
            if ($j->pop) {
                $parts[] = "POP: {$j->pop}";
            }
            if ($j->tgl_rfs_la) {
                $parts[] = "Tanggal RFS LA: {$j->tgl_rfs_la}";
            }
            if ($j->tgl_rfs_plg) {
                $parts[] = "Tanggal RFS Pelanggan: {$j->tgl_rfs_plg}";
            }
        }

        // ========================================
        // SECTION 3: PELAKSANAAN (Timeline)
        // ========================================
        if ($spk->pelaksanaan) {
            $p = $spk->pelaksanaan;
            $parts[] = "\n=== WAKTU PELAKSANAAN ===";
            $parts[] = "Permintaan Pelanggan: " . ($p->permintaan_pelanggan ?? '-');
            if ($p->datang) {
                $parts[] = "Waktu Datang: {$p->datang}";
            }
            if ($p->selesai) {
                $parts[] = "Waktu Selesai: {$p->selesai}";
            }
        }

        // ========================================
        // SECTION 4: EKSEKUSI (Teknisi & Vendor)
        // ========================================
        if ($spk->executionInfo) {
            $e = $spk->executionInfo;
            $parts[] = "\n=== INFORMASI EKSEKUSI ===";
            $parts[] = "Teknisi: {$e->teknisi}";
            $parts[] = "Nama Vendor: {$e->nama_vendor}";
            
            if ($e->pic_pelanggan) {
                $parts[] = "PIC Pelanggan: {$e->pic_pelanggan}";
            }
            if ($e->kontak_pic_pelanggan) {
                $parts[] = "Kontak PIC: {$e->kontak_pic_pelanggan}";
            }
            if ($e->latitude && $e->longitude) {
                $parts[] = "Koordinat: {$e->latitude}, {$e->longitude}";
            }
        }

        // ========================================
        // SECTION 5: INFORMASI GEDUNG (Detail Lokasi)
        // ========================================
        if ($spk->informasiGedung) {
            $g = $spk->informasiGedung;
            $parts[] = "\n=== INFORMASI GEDUNG ===";
            $parts[] = "Alamat Lengkap: {$g->alamat}";
            
            if ($g->status_gedung) {
                $parts[] = "Status Gedung: {$g->status_gedung}";
            }
            if ($g->kondisi_gedung) {
                $parts[] = "Kondisi Gedung: {$g->kondisi_gedung}";
            }
            if ($g->pemilik_bangunan) {
                $parts[] = "Pemilik Bangunan: {$g->pemilik_bangunan}";
            }
            if ($g->kontak_person) {
                $parts[] = "Contact Person: {$g->kontak_person}";
            }
            if ($g->bagian_jabatan) {
                $parts[] = "Bagian/Jabatan: {$g->bagian_jabatan}";
            }
            if ($g->telpon_fax) {
                $parts[] = "Telpon/Fax: {$g->telpon_fax}";
            }
            if ($g->email) {
                $parts[] = "Email: {$g->email}";
            }
            if ($g->jumlah_lantai_gedung) {
                $parts[] = "Jumlah Lantai: {$g->jumlah_lantai_gedung}";
            }
            if ($g->pelanggan_fo) {
                $parts[] = "Pelanggan FO: {$g->pelanggan_fo}";
            }
        }

        // ========================================
        // SECTION 6: RUANG SERVER & POWER
        // ========================================
        if ($spk->sarpenRuangServer) {
            $s = $spk->sarpenRuangServer;
            $parts[] = "\n=== SARPEN RUANG SERVER ===";
            
            if ($s->power_line_listrik) {
                $parts[] = "Power Line: {$s->power_line_listrik}";
            }
            if ($s->ketersediaan_power_outlet) {
                $parts[] = "Power Outlet: {$s->ketersediaan_power_outlet}";
            }
            if ($s->grounding_listrik) {
                $parts[] = "Grounding: {$s->grounding_listrik}";
            }
            if ($s->ups) {
                $parts[] = "UPS: {$s->ups}";
            }
            if ($s->ruangan_ber_ac) {
                $parts[] = "AC: {$s->ruangan_ber_ac}";
            }
            if ($s->suhu_ruangan_value) {
                $parts[] = "Suhu Ruangan: {$s->suhu_ruangan_value}°C";
            }
            if ($s->lantai) {
                $parts[] = "Lantai: {$s->lantai}";
            }
            if ($s->ruang) {
                $parts[] = "Ruang: {$s->ruang}";
            }
            if ($s->perangkat_pelanggan) {
                $parts[] = "Perangkat Pelanggan: {$s->perangkat_pelanggan}";
            }
        }

        // ========================================
        // SECTION 7: LOKASI ANTENA (Wireless)
        // ========================================
        if ($spk->lokasiAntena) {
            $a = $spk->lokasiAntena;
            $parts[] = "\n=== LOKASI ANTENA ===";
            
            if ($a->lokasi_antena) {
                $parts[] = "Lokasi Antena: {$a->lokasi_antena}";
            }
            if ($a->detail_lokasi_antena) {
                $parts[] = "Detail Lokasi: {$a->detail_lokasi_antena}";
            }
            if ($a->space_tersedia) {
                $parts[] = "Space Tersedia: {$a->space_tersedia}";
            }
            if ($a->akses_di_lokasi_perlu_alat_bantu) {
                $parts[] = "Akses Lokasi: {$a->akses_di_lokasi_perlu_alat_bantu}";
            }
            if ($a->penangkal_petir) {
                $parts[] = "Penangkal Petir: {$a->penangkal_petir}";
            }
            if ($a->tower_pole) {
                $parts[] = "Tower/Pole: {$a->tower_pole}";
            }
            if ($a->pemilik_tower_pole) {
                $parts[] = "Pemilik Tower: {$a->pemilik_tower_pole}";
            }
        }

        // ========================================
        // SECTION 8: PERIZINAN & BIAYA GEDUNG
        // ========================================
        if ($spk->perizinanBiayaGedung) {
            $pb = $spk->perizinanBiayaGedung;
            $parts[] = "\n=== PERIZINAN & BIAYA GEDUNG ===";
            
            if ($pb->pic_bm) {
                $parts[] = "PIC Building Management: {$pb->pic_bm}";
            }
            if ($pb->material_dan_infrastruktur) {
                $parts[] = "Material & Infrastruktur: {$pb->material_dan_infrastruktur}";
            }
            if ($pb->panjang_kabel_dalam_gedung) {
                $parts[] = "Panjang Kabel: {$pb->panjang_kabel_dalam_gedung}";
            }
            if ($pb->pelaksana_penarikan_kabel_dalam_gedung) {
                $parts[] = "Pelaksana: {$pb->pelaksana_penarikan_kabel_dalam_gedung}";
            }
        }

        // ========================================
        // SECTION 9: PENEMPATAN PERANGKAT
        // ========================================
        if ($spk->penempatanPerangkat) {
            $pp = $spk->penempatanPerangkat;
            $parts[] = "\n=== PENEMPATAN PERANGKAT ===";
            
            if ($pp->lokasi_penempatan_modem_dan_router) {
                $parts[] = "Lokasi Modem & Router: {$pp->lokasi_penempatan_modem_dan_router}";
            }
            if ($pp->kesiapan_ruang_server) {
                $parts[] = "Kesiapan Ruang Server: {$pp->kesiapan_ruang_server}";
            }
            if ($pp->ketersedian_rak_server) {
                $parts[] = "Rak Server: {$pp->ketersedian_rak_server}";
            }
        }

        // ========================================
        // SECTION 10: KAWASAN (Private & Umum)
        // ========================================
        if ($spk->perizinanBiayaKawasan) {
            $pk = $spk->perizinanBiayaKawasan;
            $parts[] = "\n=== PERIZINAN KAWASAN PRIVATE ===";
            $parts[] = "Melewati Kawasan Private: {$pk->melewati_kawasan_private}";
            
            if ($pk->nama_kawasan) {
                $parts[] = "Nama Kawasan: {$pk->nama_kawasan}";
            }
            if ($pk->pic_kawasan) {
                $parts[] = "PIC Kawasan: {$pk->pic_kawasan}";
            }
        }

        if ($spk->kawasanUmum) {
            $ku = $spk->kawasanUmum;
            $parts[] = "\n=== KAWASAN UMUM ===";
            
            if ($ku->nama_kawasan_umum_pu_yang_dilewati) {
                $parts[] = "Kawasan Umum: {$ku->nama_kawasan_umum_pu_yang_dilewati}";
            }
            if ($ku->panjang_jalur_outdoor_di_kawasan_umum) {
                $parts[] = "Panjang Jalur: {$ku->panjang_jalur_outdoor_di_kawasan_umum}";
            }
        }

        // ========================================
        // SECTION 11: DATA SPLITTER
        // ========================================
        if ($spk->dataSplitter) {
            $ds = $spk->dataSplitter;
            $parts[] = "\n=== DATA SPLITTER ===";
            
            if ($ds->lokasi_splitter) {
                $parts[] = "Lokasi Splitter: {$ds->lokasi_splitter}";
            }
            if ($ds->id_splitter_text) {
                $parts[] = "ID Splitter: {$ds->id_splitter_text}";
            }
            if ($ds->kapasitas_splitter) {
                $parts[] = "Kapasitas: {$ds->kapasitas_splitter}";
            }
            if ($ds->jumlah_port_kosong) {
                $parts[] = "Port Kosong: {$ds->jumlah_port_kosong}";
            }
            if ($ds->nama_node_jika_tidak_ada_splitter) {
                $parts[] = "Node: {$ds->nama_node_jika_tidak_ada_splitter}";
            }
        }

        // ========================================
        // SECTION 12: HANDHOLE EKSISTING
        // ========================================
        if ($spk->hhEksisting && $spk->hhEksisting->count() > 0) {
            $parts[] = "\n=== HANDHOLE EKSISTING ===";
            foreach ($spk->hhEksisting as $hh) {
                $parts[] = "HH-{$hh->nomor_hh}: Kondisi {$hh->kondisi_hh}, Lokasi: {$hh->lokasi_hh}";
                if ($hh->ketersediaan_closure) {
                    $parts[] = "  Closure: {$hh->ketersediaan_closure}";
                }
                if ($hh->kapasitas_closure) {
                    $parts[] = "  Kapasitas: {$hh->kapasitas_closure}";
                }
            }
        }

        // ========================================
        // SECTION 13: HANDHOLE BARU
        // ========================================
        if ($spk->hhBaru && $spk->hhBaru->count() > 0) {
            $parts[] = "\n=== HANDHOLE BARU ===";
            foreach ($spk->hhBaru as $hh) {
                $parts[] = "HH Baru-{$hh->nomor_hh}: Lokasi {$hh->lokasi_hh}";
                if ($hh->kebutuhan_penambahan_closure) {
                    $parts[] = "  Kebutuhan Closure: {$hh->kebutuhan_penambahan_closure}";
                }
            }
        }

        return implode("\n", array_filter($parts));
    }

    /**
     * Call Flask API untuk generate embedding
     */
    private function callFlaskEmbedding(string $text): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->flaskApiUrl}/generate-embedding", [
                    'text' => $text,
                    'model' => $this->embeddingModel
                ]);

            if (!$response->successful()) {
                throw new Exception("Flask API error: " . $response->body());
            }

            $data = $response->json();

            if (!isset($data['embedding']) || !is_array($data['embedding'])) {
                throw new Exception("Invalid embedding response from Flask API");
            }

            return $data['embedding'];

        } catch (Exception $e) {
            Log::error('Failed to call Flask embedding API', [
                'error' => $e->getMessage(),
                'url' => $this->flaskApiUrl
            ]);
            throw $e;
        }
    }

    /**
     * Generate embedding untuk satu text (helper method)
     */
    public function generateEmbedding(string $text): array
    {
        return $this->callFlaskEmbedding($text);
    }
}