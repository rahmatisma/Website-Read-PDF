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

    public $timeout = 900; // 15 menit timeout untuk job
    public $tries = 1; // Cuma coba 1x, kalau gagal ya gagal

    protected $uploadId;

    /**
     * Create a new job instance.
     */
    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     */
    public function handle(JsonToDatabase $jsonToDatabase): void
    {
        try {
            // Ambil data upload
            $upload = Document::find($this->uploadId);
            
            if (!$upload) {
                Log::error('Upload not found', ['upload_id' => $this->uploadId]);
                return;
            }

            // Update status jadi processing
            $upload->update(['status' => 'processing']);

            Log::info('Processing document started', [
                'upload_id' => $upload->id_upload,
                'file_name' => $upload->file_name,
                'file_size' => $upload->file_size,
                'timestamp' => now()
            ]);

            // Ambil full path file
            $fullPath = storage_path('app/public/' . $upload->file_path);

            if (!file_exists($fullPath)) {
                throw new \Exception('File not found: ' . $fullPath);
            }

            Log::info('Sending file to Python API', [
                'upload_id' => $upload->id_upload,
                'file_path' => $fullPath,
                'python_url' => 'http://localhost:5000/process-pdf'
            ]);

            // ✅ OPTIMIZED: Kirim ke Python dengan streaming (tidak load semua ke memory)
            $response = Http::timeout(600)
                ->attach(
                    'file',
                    fopen($fullPath, 'r'), // ⬅️ Stream file, hemat memory
                    $upload->file_name
                )
                ->post('http://localhost:5000/process-pdf');

            // Cek response
            if ($response->successful()) {
                $result = $response->json();

                Log::info('Python processing successful', [
                    'upload_id' => $upload->id_upload,
                    'has_data' => isset($result['data'])
                ]);

                // Simpan JSON hasil ke database
                $upload->update([
                    'extracted_data' => $result,
                ]);

                Log::info('Extracted data saved, starting database split', [
                    'upload_id' => $upload->id_upload
                ]);

                // Proses ke database (pecah ke tabel-tabel)
                try {
                    $jsonToDatabase->process($result, $upload->id_upload);

                    // Update status jadi completed
                    $upload->update(['status' => 'completed']);

                    Log::info('Document processing completed successfully', [
                        'upload_id' => $upload->id_upload,
                        'file_name' => $upload->file_name,
                        'duration' => now()->diffInSeconds($upload->created_at) . ' seconds'
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to split data to database', [
                        'upload_id' => $upload->id_upload,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    // Update status failed
                    $upload->update(['status' => 'failed']);

                    throw $e;
                }

            } else {
                $errorBody = $response->body();
                
                Log::error('Python API returned error', [
                    'upload_id' => $upload->id_upload,
                    'status_code' => $response->status(),
                    'error_body' => $errorBody
                ]);

                throw new \Exception('Python API error: ' . $errorBody);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Cannot connect to Python API', [
                'upload_id' => $this->uploadId,
                'error' => $e->getMessage(),
                'python_url' => 'http://localhost:5000/process-pdf'
            ]);

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;

        } catch (\Exception $e) {
            Log::error('Document processing failed', [
                'upload_id' => $this->uploadId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            Document::where('id_upload', $this->uploadId)
                ->update(['status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessDocumentJob failed permanently', [
            'upload_id' => $this->uploadId,
            'error' => $exception->getMessage(),
            'class' => get_class($exception)
        ]);

        Document::where('id_upload', $this->uploadId)
            ->update(['status' => 'failed']);
    }
}