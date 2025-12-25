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

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ✅ EXTENDED TIMEOUT untuk OCR yang lama
    public $timeout = 2400; // 40 menit (2400 detik)
    public $tries = 1;

    protected $uploadId;

    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

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

            Log::info('🚀 JOB STARTED (OCR Mode)', [
                'upload_id' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'file_size' => $upload->file_size . ' bytes',
                'current_status' => $upload->status,
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

                // ✅ 6. Simpan extracted data
                $upload->update([
                    'extracted_data' => $result,
                ]);

                Log::info('💾 Extracted data SAVED', [
                    'upload_id' => $upload->id_upload,
                    'elapsed' => round(microtime(true) - $startTime, 2) . 's'
                ]);

                // ✅ 7. Proses ke database (split data)
                try {
                    Log::info('🔄 Starting database split', [
                        'upload_id' => $upload->id_upload
                    ]);

                    $jsonToDatabase->process($result, $upload->id_upload);

                    // ✅ 8. Update status → completed
                    $upload->update(['status' => 'completed']);

                    $totalTime = round(microtime(true) - $startTime, 2);
                    $totalMinutes = round($totalTime / 60, 2);

                    Log::info('✅ JOB COMPLETED SUCCESSFULLY', [
                        'upload_id' => $upload->id_upload,
                        'file_name' => $upload->file_name,
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
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'processing_time' => $totalMinutes . ' minutes (' . $totalTime . 's)',
                'trace' => $e->getTraceAsString()
            ]);

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('❌ JOB FAILED PERMANENTLY', [
            'upload_id' => $this->uploadId,
            'error' => $exception->getMessage(),
            'class' => get_class($exception),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);

        Document::where('id_upload', $this->uploadId)
            ->update(['status' => 'failed']);
    }
}