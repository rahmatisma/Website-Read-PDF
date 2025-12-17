<?php

namespace App\Services;

use App\Models\Jaringan;
use App\Models\Spk;
use App\Models\JaringanEmbedding;
use App\Models\SpkEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class EmbeddingService
{
    protected string $flaskApiUrl;
    protected string $embeddingModel;
    protected int $embeddingDimension;
    protected int $timeout;

    public function __construct()
    {
        // Config Flask API URL (bisa diatur di .env)
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->embeddingModel = env('EMBEDDING_MODEL', 'nomic-embed-text');
        $this->embeddingDimension = env('EMBEDDING_DIMENSION', 384);
        $this->timeout = env('FLASK_API_TIMEOUT', 300);
    }

    /**
     * Generate embedding untuk data JARINGAN
     */
    public function generateJaringanEmbedding(string $noJaringan): ?JaringanEmbedding
    {
        try {
            // 1. Ambil data JARINGAN dari database
            $jaringan = Jaringan::where('no_jaringan', $noJaringan)->first();
            
            if (!$jaringan) {
                throw new Exception("Jaringan with no_jaringan {$noJaringan} not found");
            }

            // 2. Build content text
            $contentText = $this->buildJaringanContentText($jaringan);

            // 3. Generate embedding via Flask API
            $embedding = $this->callFlaskEmbedding($contentText);

            // 4. Save to database
            $jaringanEmbedding = JaringanEmbedding::updateOrCreate(
                ['no_jaringan' => $noJaringan],
                [
                    'content_text' => $contentText,
                    'embedding' => json_encode($embedding),
                    'embedding_model' => $this->embeddingModel,
                    'embedding_dimension' => $this->embeddingDimension,
                ]
            );

            Log::info('Jaringan embedding generated', [
                'no_jaringan' => $noJaringan,
                'content_length' => strlen($contentText),
                'embedding_dimension' => count($embedding)
            ]);

            return $jaringanEmbedding;

        } catch (Exception $e) {
            Log::error('Failed to generate jaringan embedding', [
                'no_jaringan' => $noJaringan,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate embedding untuk data SPK (dengan semua child data)
     */
    public function generateSpkEmbedding(int $idSpk): ?SpkEmbedding
    {
        try {
            // 1. Ambil data SPK dengan semua relasi
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

            // 2. Build content text
            $contentText = $this->buildSpkContentText($spk);

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
     * Build content text untuk JARINGAN
     */
    private function buildJaringanContentText(Jaringan $jaringan): string
    {
        $parts = [];

        $parts[] = "Nomor Jaringan: {$jaringan->no_jaringan}";
        
        if ($jaringan->nama_pelanggan) {
            $parts[] = "Pelanggan: {$jaringan->nama_pelanggan}";
        }
        
        if ($jaringan->lokasi_pelanggan) {
            $parts[] = "Lokasi: {$jaringan->lokasi_pelanggan}";
        }
        
        if ($jaringan->jasa) {
            $parts[] = "Jasa: {$jaringan->jasa}";
        }
        
        if ($jaringan->media_akses) {
            $parts[] = "Media Akses: {$jaringan->media_akses}";
        }
        
        if ($jaringan->kecepatan) {
            $parts[] = "Kecepatan: {$jaringan->kecepatan}";
        }
        
        if ($jaringan->pop) {
            $parts[] = "POP: {$jaringan->pop}";
        }
        
        if ($jaringan->tgl_rfs_plg) {
            $parts[] = "Tanggal RFS Pelanggan: {$jaringan->tgl_rfs_plg}";
        }

        return implode("\n", $parts);
    }

    /**
     * Build content text untuk SPK (dengan semua detail)
     */
    private function buildSpkContentText(Spk $spk): string
    {
        $parts = [];

        // SPK Header
        $parts[] = "=== INFORMASI SPK ===";
        $parts[] = "No SPK: {$spk->no_spk}";
        $parts[] = "Jenis SPK: {$spk->jenis_spk}";
        $parts[] = "Tanggal SPK: {$spk->tanggal_spk}";
        
        // Jaringan & Pelanggan
        if ($spk->jaringan) {
            $parts[] = "\n=== INFORMASI PELANGGAN ===";
            $parts[] = "Nomor Jaringan: {$spk->jaringan->no_jaringan}";
            $parts[] = "Nama Pelanggan: {$spk->jaringan->nama_pelanggan}";
            $parts[] = "Lokasi: {$spk->jaringan->lokasi_pelanggan}";
            $parts[] = "Jasa: {$spk->jaringan->jasa}";
        }

        // Pelaksanaan
        if ($spk->pelaksanaan) {
            $parts[] = "\n=== PELAKSANAAN ===";
            $parts[] = "Permintaan: {$spk->pelaksanaan->permintaan_pelanggan}";
            if ($spk->pelaksanaan->datang) {
                $parts[] = "Datang: {$spk->pelaksanaan->datang}";
            }
            if ($spk->pelaksanaan->selesai) {
                $parts[] = "Selesai: {$spk->pelaksanaan->selesai}";
            }
        }

        // Execution Info
        if ($spk->executionInfo) {
            $parts[] = "\n=== EKSEKUSI ===";
            $parts[] = "Teknisi: {$spk->executionInfo->teknisi}";
            $parts[] = "Vendor: {$spk->executionInfo->nama_vendor}";
            if ($spk->executionInfo->pic_pelanggan) {
                $parts[] = "PIC Pelanggan: {$spk->executionInfo->pic_pelanggan}";
            }
        }

        // Informasi Gedung
        if ($spk->informasiGedung) {
            $parts[] = "\n=== INFORMASI GEDUNG ===";
            $parts[] = "Alamat: {$spk->informasiGedung->alamat}";
            if ($spk->informasiGedung->status_gedung) {
                $parts[] = "Status Gedung: {$spk->informasiGedung->status_gedung}";
            }
            if ($spk->informasiGedung->pemilik_bangunan) {
                $parts[] = "Pemilik: {$spk->informasiGedung->pemilik_bangunan}";
            }
        }

        // Sarpen Ruang Server
        if ($spk->sarpenRuangServer) {
            $parts[] = "\n=== RUANG SERVER ===";
            if ($spk->sarpenRuangServer->ruangan_ber_ac) {
                $parts[] = "AC: {$spk->sarpenRuangServer->ruangan_ber_ac}";
            }
            if ($spk->sarpenRuangServer->suhu_ruangan_value) {
                $parts[] = "Suhu: {$spk->sarpenRuangServer->suhu_ruangan_value}°C";
            }
        }

        // Data Splitter
        if ($spk->dataSplitter) {
            $parts[] = "\n=== DATA SPLITTER ===";
            if ($spk->dataSplitter->lokasi_splitter) {
                $parts[] = "Lokasi: {$spk->dataSplitter->lokasi_splitter}";
            }
            if ($spk->dataSplitter->kapasitas_splitter) {
                $parts[] = "Kapasitas: {$spk->dataSplitter->kapasitas_splitter}";
            }
        }

        // HH Eksisting
        if ($spk->hhEksisting && $spk->hhEksisting->count() > 0) {
            $parts[] = "\n=== HANDHOLE EKSISTING ===";
            foreach ($spk->hhEksisting as $hh) {
                $parts[] = "HH #{$hh->nomor_hh}: {$hh->kondisi_hh} - {$hh->lokasi_hh}";
            }
        }

        // HH Baru
        if ($spk->hhBaru && $spk->hhBaru->count() > 0) {
            $parts[] = "\n=== HANDHOLE BARU ===";
            foreach ($spk->hhBaru as $hh) {
                $parts[] = "HH Baru #{$hh->nomor_hh}: {$hh->lokasi_hh}";
            }
        }

        return implode("\n", $parts);
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