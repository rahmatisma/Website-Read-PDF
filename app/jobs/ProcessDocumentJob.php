<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\JsonToDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 menit
    public $tries = 1;

    protected $uploadId;
    protected $expectedCategory;

    public function __construct(int $uploadId, string $expectedCategory)
    {
        $this->uploadId = $uploadId;
        $this->expectedCategory = $expectedCategory;
    }

    public function handle(JsonToDatabase $jsonToDatabase)
    {
        $upload = Document::find($this->uploadId);

        if (!$upload) {
            Log::error('Document not found for processing', ['id' => $this->uploadId]);
            return;
        }

        try {
            // ========================================
            // STEP 1: UPDATE STATUS KE PROCESSING
            // ========================================
            $upload->update(['status' => 'processing']);
            
            Log::info('🔄 Starting document processing', [
                'id_upload' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'expected_category' => $this->expectedCategory
            ]);

            // ========================================
            // STEP 2: VALIDASI HALAMAN PERTAMA
            // ========================================
            $validationResult = $this->validateFirstPage($upload);
            
            if (!$validationResult['success']) {
                throw new \Exception($validationResult['message']);
            }

            if (!$validationResult['is_valid_for_category']) {
                throw new \Exception($validationResult['message']);
            }

            Log::info(' Validation passed', [
                'document_type' => $validationResult['document_type'],
                'confidence' => $validationResult['confidence']
            ]);

            // ========================================
            // STEP 3: FULL PROCESSING
            // ========================================
            $processingResult = $this->processFullDocument($upload);

            if (!$processingResult['success']) {
                throw new \Exception($processingResult['message']);
            }

            // ========================================
            // STEP 4: SIMPAN KE DATABASE (🔧 FIXED)
            // ========================================
            $jsonData = $processingResult['data'];
            
            // 🔧 FIX: Wrap data in proper structure for JsonToDatabase
            // Python returns: { "dokumentasi": [...], "parsed": {...} }
            // But JsonToDatabase expects: { "data": { "parsed": {...}, "dokumentasi": [...] } }
            $wrappedJsonData = [
                'data' => $jsonData
            ];
            
            Log::info('📦 Wrapped JSON structure for database', [
                'original_keys' => array_keys($jsonData),
                'wrapped_keys' => array_keys($wrappedJsonData),
                'has_data_key' => isset($wrappedJsonData['data']),
                'has_parsed_key' => isset($wrappedJsonData['data']['parsed']),
            ]);
            
            $jsonToDatabase->process($wrappedJsonData, $upload->id_upload);

            // ========================================
            // STEP 5: UPDATE STATUS KE COMPLETED
            // ========================================
            
            // 🔧 Normalize extracted_data structure for frontend
            // Frontend expects: { parsed: { data: {...}, document_type: "..." }, dokumentasi: [...] }
            $normalizedData = $this->normalizeExtractedData($jsonData);
            
            $upload->update([
                'status' => 'completed',
                'extracted_data' => $normalizedData
            ]);

            Log::info(' Document processing completed', [
                'id_upload' => $upload->id_upload,
                'document_type' => $validationResult['document_type'],
                'total_images' => count($jsonData['dokumentasi'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('Document processing failed', [
                'id_upload' => $upload->id_upload,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $upload->update([
                'status' => 'failed',
                'extracted_data' => [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toDateTimeString()
                ]
            ]);

            // Hapus file jika validasi gagal
            if (str_contains($e->getMessage(), 'tidak sesuai') || 
                str_contains($e->getMessage(), 'tidak dapat dideteksi')) {
                
                Log::info('🗑️ Deleting invalid document file', [
                    'id_upload' => $upload->id_upload,
                    'file_path' => $upload->file_path,
                    'reason' => 'validation_failed'
                ]);

                if (Storage::exists('public/' . $upload->file_path)) {
                    Storage::delete('public/' . $upload->file_path);
                }
            }
        }
    }

    /**
     * ========================================
     * VALIDASI HALAMAN PERTAMA
     * ========================================
     */
    private function validateFirstPage(Document $upload): array
    {
        $filePath = storage_path('app/public/' . $upload->file_path);

        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File tidak ditemukan di storage',
                'is_valid_for_category' => false
            ];
        }

        try {
            Log::info('🔍 Validating first page via Python API', [
                'file_path' => $filePath,
                'expected_category' => $this->expectedCategory
            ]);

            $response = Http::timeout(30)
                ->attach('file', file_get_contents($filePath), $upload->file_name)
                ->post('http://127.0.0.1:5000/validate-first-page', [
                    'expected_category' => $this->expectedCategory
                ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                return [
                    'success' => false,
                    'message' => $errorData['message'] ?? 'Validasi gagal',
                    'is_valid_for_category' => false
                ];
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Validation API error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal berkomunikasi dengan API validasi: ' . $e->getMessage(),
                'is_valid_for_category' => false
            ];
        }
    }

    /**
     * ========================================
     * FULL PROCESSING
     * ========================================
     */
    private function processFullDocument(Document $upload): array
    {
        $filePath = storage_path('app/public/' . $upload->file_path);
        $laravelStoragePath = storage_path('app/public');

        try {
            Log::info('📄 Full processing via Python API', [
                'file_path' => $filePath,
                'laravel_storage_path' => $laravelStoragePath
            ]);

            $response = Http::timeout(300)
                ->asMultipart()
                ->attach('file', file_get_contents($filePath), $upload->file_name)
                ->attach('laravel_storage_path', $laravelStoragePath)
                ->post('http://127.0.0.1:5000/process-pdf');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Python API error: ' . $response->body()
                ];
            }

            $result = $response->json();

            $imageCount = count($result['data']['dokumentasi'] ?? []);
            Log::info(' Python processing completed', [
                'total_images' => $imageCount
            ]);

            if ($imageCount > 0) {
                $sampleImage = $result['data']['dokumentasi'][0];
                Log::info('📸 Sample image path', [
                    'jenis' => $sampleImage['jenis'] ?? 'N/A',
                    'patch_foto' => $sampleImage['patch_foto'] ?? 'N/A'
                ]);
            }

            return [
                'success' => true,
                'data' => $result['data'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Processing API error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memproses dokumen: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ========================================
     * NORMALIZE EXTRACTED DATA FOR FRONTEND
     * ========================================
     * 
     * Frontend expects consistent structure:
     * {
     *   "parsed": {
     *     "document_type": "spk_survey",
     *     "data": { spk: {...}, pelanggan: {...}, ... }
     *   },
     *   "dokumentasi": [...]
     * }
     */
    private function normalizeExtractedData(array $jsonData): array
    {
        $dokumentasi = $jsonData['dokumentasi'] ?? [];
        
        // Case 1: Structure is already normalized (parsed.data exists)
        if (isset($jsonData['parsed']['data'])) {
            return [
                'parsed' => $jsonData['parsed'],
                'dokumentasi' => $dokumentasi
            ];
        }
        
        // Case 2: Nested parsed.parsed (current Python output for SPK)
        if (isset($jsonData['parsed']['parsed'])) {
            return [
                'parsed' => [
                    'document_type' => $jsonData['parsed']['document_type'] ?? 'unknown',
                    'metadata' => $jsonData['parsed']['metadata'] ?? [],
                    'data' => $jsonData['parsed']['parsed']  // ← Move parsed.parsed to parsed.data
                ],
                'dokumentasi' => $dokumentasi
            ];
        }
        
        // Case 3: Fallback - wrap everything in parsed.data
        return [
            'parsed' => [
                'document_type' => $jsonData['parsed']['document_type'] ?? 'unknown',
                'data' => $jsonData['parsed'] ?? []
            ],
            'dokumentasi' => $dokumentasi
        ];
    }
}