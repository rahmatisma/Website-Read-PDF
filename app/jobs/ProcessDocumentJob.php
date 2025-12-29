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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ✅ EXTENDED TIMEOUT untuk OCR yang lama
    public $timeout = 2400; // 40 menit (2400 detik)
    public $tries = 1;

    protected $uploadId;
    protected $expectedCategory; // 'spk' atau 'checklist'

    /**
     * Create a new job instance.
     */
    public function __construct($uploadId, $expectedCategory = 'spk')
    {
        $this->uploadId = $uploadId;
        $this->expectedCategory = $expectedCategory;
    }

    /**
     * ========================================
     * VALIDASI DOCUMENT TYPE
     * ========================================
     */
    private function validateDocumentType(string $detectedType, string $expectedCategory): array
    {
        // Definisi kategori dokumen
        $spkTypes = ['spk_survey', 'spk_instalasi', 'spk_dismantle', 'spk_aktivasi'];
        $checklistTypes = ['checklist_wireline', 'checklist_wireless'];
        
        $isValid = false;
        $message = '';
        
        if ($expectedCategory === 'spk') {
            $isValid = in_array($detectedType, $spkTypes);
            if (!$isValid) {
                if (in_array($detectedType, $checklistTypes)) {
                    $message = "❌ VALIDASI GAGAL: Dokumen ini adalah Form Checklist ({$detectedType}), bukan SPK! Silakan upload di halaman Form Checklist.";
                } elseif ($detectedType === 'unknown') {
                    $message = "❌ VALIDASI GAGAL: Jenis dokumen tidak dapat dideteksi. Pastikan file PDF adalah dokumen SPK yang valid (Survey, Instalasi, Dismantle, atau Aktivasi).";
                } else {
                    $message = "❌ VALIDASI GAGAL: Jenis dokumen ({$detectedType}) tidak sesuai untuk upload SPK.";
                }
            }
        } elseif ($expectedCategory === 'checklist') {
            $isValid = in_array($detectedType, $checklistTypes);
            if (!$isValid) {
                if (in_array($detectedType, $spkTypes)) {
                    $message = "❌ VALIDASI GAGAL: Dokumen ini adalah SPK ({$detectedType}), bukan Form Checklist! Silakan upload di halaman Dokumen PDF.";
                } elseif ($detectedType === 'unknown') {
                    $message = "❌ VALIDASI GAGAL: Jenis dokumen tidak dapat dideteksi. Pastikan file PDF adalah Form Checklist yang valid (Wireline atau Wireless).";
                } else {
                    $message = "❌ VALIDASI GAGAL: Jenis dokumen ({$detectedType}) tidak sesuai untuk upload Form Checklist.";
                }
            }
        }
        
        return [
            'valid' => $isValid,
            'message' => $message,
            'detected_type' => $detectedType
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(JsonToDatabase $jsonToDatabase): void
    {
        $startTime = microtime(true);
        
        try {
            // ✅ 1. Ambil data upload
            $upload = Document::find($this->uploadId);
            
            if (!$upload) {
                Log::error('❌ Upload not found', ['upload_id' => $this->uploadId]);
                return;
            }

            Log::info('🚀 JOB STARTED (OCR Mode with Validation)', [
                'upload_id' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'file_size' => $upload->file_size . ' bytes',
                'current_status' => $upload->status,
                'expected_category' => $this->expectedCategory,
                'max_timeout' => '40 minutes',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

            // ✅ 2. Update status → processing
            $upload->update(['status' => 'processing']);
            
            Log::info('📝 Status updated to PROCESSING', [
                'upload_id' => $upload->id_upload,
                'elapsed' => round(microtime(true) - $startTime, 2) . 's'
            ]);

            // ✅ 3. Validasi file exists
            $fullPath = storage_path('app/public/' . $upload->file_path);

            if (!file_exists($fullPath)) {
                throw new \Exception('File not found: ' . $fullPath);
            }

            $fileSize = filesize($fullPath);
            $fileSizeMB = round($fileSize / (1024 * 1024), 2);

            Log::info('📤 Sending file to Python OCR API', [
                'upload_id' => $upload->id_upload,
                'file_path' => $fullPath,
                'file_size' => $fileSizeMB . ' MB',
                'python_url' => 'http://localhost:5000/process-pdf',
                'estimated_time' => 'Could take 15-30 minutes for OCR processing'
            ]);

            // ✅ 4. Kirim ke Python API dengan EXTENDED TIMEOUT
            // Timeout 35 menit (2100 detik) - lebih kecil dari job timeout
            $response = Http::timeout(2100) // ⬅️ 35 menit untuk HTTP request
                ->attach(
                    'file',
                    fopen($fullPath, 'r'),
                    $upload->file_name
                )
                ->post('http://localhost:5000/process-pdf');

            // ✅ 5. Handle response
            if ($response->successful()) {
                $result = $response->json();

                $processingTime = round(microtime(true) - $startTime, 2);
                $processingMinutes = round($processingTime / 60, 2);

                Log::info('✅ Python OCR processing SUCCESSFUL', [
                    'upload_id' => $upload->id_upload,
                    'has_data' => isset($result['data']),
                    'response_size' => strlen(json_encode($result)) . ' bytes',
                    'processing_time' => $processingMinutes . ' minutes (' . $processingTime . 's)',
                    'elapsed' => $processingTime . 's'
                ]);

                // ✅ 6. VALIDASI DOCUMENT TYPE (CRITICAL!)
                // Cek struktur response dari Python
                if (!isset($result['data']['parsed']['document_type'])) {
                    Log::error('❌ Invalid Python response structure', [
                        'upload_id' => $upload->id_upload,
                        'response_keys' => array_keys($result),
                        'data_keys' => isset($result['data']) ? array_keys($result['data']) : 'N/A',
                        'parsed_keys' => isset($result['data']['parsed']) ? array_keys($result['data']['parsed']) : 'N/A'
                    ]);
                    
                    throw new \Exception("Response dari Python tidak mengandung document_type. Struktur response tidak valid.");
                }

                $detectedType = $result['data']['parsed']['document_type'];
                
                Log::info('🔍 Validating Document Type', [
                    'upload_id' => $upload->id_upload,
                    'detected_type' => $detectedType,
                    'expected_category' => $this->expectedCategory,
                    'file_name' => $upload->file_name
                ]);

                $validation = $this->validateDocumentType($detectedType, $this->expectedCategory);

                if (!$validation['valid']) {
                    // ❌ VALIDASI GAGAL - Hapus file dan record
                    Log::error('🚫 DOCUMENT TYPE VALIDATION FAILED', [
                        'upload_id' => $upload->id_upload,
                        'file_name' => $upload->file_name,
                        'detected_type' => $detectedType,
                        'expected_category' => $this->expectedCategory,
                        'validation_message' => $validation['message'],
                        'action' => 'Deleting file and database record'
                    ]);

                    // Hapus file dari storage
                    if (Storage::exists('public/' . $upload->file_path)) {
                        Storage::delete('public/' . $upload->file_path);
                        Log::info('🗑️ File deleted from storage', [
                            'file_path' => $upload->file_path
                        ]);
                    }

                    // Hapus record dari database
                    $upload->delete();
                    Log::info('🗑️ Database record deleted', [
                        'upload_id' => $upload->id_upload
                    ]);

                    // Throw exception dengan pesan yang jelas
                    throw new \Exception($validation['message']);
                }

                Log::info('✅ Document type validation PASSED', [
                    'upload_id' => $upload->id_upload,
                    'detected_type' => $detectedType,
                    'expected_category' => $this->expectedCategory,
                    'validation_status' => 'VALID'
                ]);

                // ✅ 7. Simpan extracted data
                $upload->update([
                    'extracted_data' => $result,
                ]);

                Log::info('💾 Extracted data SAVED', [
                    'upload_id' => $upload->id_upload,
                    'elapsed' => round(microtime(true) - $startTime, 2) . 's'
                ]);

                // ✅ 8. Proses ke database (split data)
                try {
                    Log::info('🔄 Starting database split', [
                        'upload_id' => $upload->id_upload
                    ]);

                    $jsonToDatabase->process($result, $upload->id_upload);

                    // ✅ 9. Update status → completed
                    $upload->update(['status' => 'completed']);

                    $totalTime = round(microtime(true) - $startTime, 2);
                    $totalMinutes = round($totalTime / 60, 2);

                    Log::info('✅ JOB COMPLETED SUCCESSFULLY', [
                        'upload_id' => $upload->id_upload,
                        'file_name' => $upload->file_name,
                        'document_type' => $detectedType,
                        'category' => $this->expectedCategory,
                        'total_duration' => $totalMinutes . ' minutes (' . $totalTime . 's)',
                        'status' => 'completed'
                    ]);

                } catch (\Exception $e) {
                    Log::error('❌ Database split FAILED', [
                        'upload_id' => $upload->id_upload,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);

                    $upload->update(['status' => 'failed']);
                    throw $e;
                }

            } else {
                $errorBody = $response->body();
                
                Log::error('❌ Python OCR API ERROR', [
                    'upload_id' => $upload->id_upload,
                    'status_code' => $response->status(),
                    'error_body' => substr($errorBody, 0, 500),
                    'elapsed' => round(microtime(true) - $startTime, 2) . 's'
                ]);

                $upload->update(['status' => 'failed']);
                throw new \Exception('Python API error: ' . $errorBody);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('❌ CANNOT CONNECT to Python API', [
                'upload_id' => $this->uploadId,
                'error' => $e->getMessage(),
                'python_url' => 'http://localhost:5000/process-pdf',
                'suggestion' => 'Make sure Python Flask is running on port 5000'
            ]);

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $totalTime = round(microtime(true) - $startTime, 2);
            $totalMinutes = round($totalTime / 60, 2);
            
            // Cek apakah timeout
            if (strpos($e->getMessage(), 'timed out') !== false || 
                strpos($e->getMessage(), 'timeout') !== false) {
                
                Log::error('⏱️ HTTP REQUEST TIMEOUT', [
                    'upload_id' => $this->uploadId,
                    'error' => $e->getMessage(),
                    'processing_time' => $totalMinutes . ' minutes',
                    'timeout_limit' => '35 minutes',
                    'suggestion' => 'OCR took longer than expected. Consider increasing timeout or optimizing OCR process.'
                ]);
            } else {
                Log::error('❌ HTTP REQUEST ERROR', [
                    'upload_id' => $this->uploadId,
                    'error' => $e->getMessage(),
                    'processing_time' => $totalMinutes . ' minutes'
                ]);
            }

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;

        } catch (\Exception $e) {
            $totalTime = round(microtime(true) - $startTime, 2);
            $totalMinutes = round($totalTime / 60, 2);
            
            Log::error('❌ JOB FAILED', [
                'upload_id' => $this->uploadId,
                'expected_category' => $this->expectedCategory,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'processing_time' => $totalMinutes . ' minutes (' . $totalTime . 's)',
                'trace' => substr($e->getTraceAsString(), 0, 1000) // Limit trace length
            ]);

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ JOB FAILED PERMANENTLY', [
            'upload_id' => $this->uploadId,
            'expected_category' => $this->expectedCategory,
            'error' => $exception->getMessage(),
            'class' => get_class($exception),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);

        Document::where('id_upload', $this->uploadId)
            ->update(['status' => 'failed']);
    }
}