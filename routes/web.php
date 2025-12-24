<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FormChecklistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'dashboard'])->name('dashboard');

    // ========================================
    // DOCUMENTS ROUTES (Struktur Lama Anda)
    // ========================================
    Route::prefix('documents')->name('documents.')->group(function () {
        // Upload dokumen
        Route::post('/pdf', [UploadController::class, 'storePDF'])->name('store.pdf');
        Route::post('/image', [UploadController::class, 'storeImage'])->name('store.image');
        Route::post('/doc', [UploadController::class, 'storeDoc'])->name('store.doc');
        
        // ⬇️ ROUTE SPESIFIK HARUS DI ATAS (sebelum {type} atau {id})
        
        // ✅ API untuk polling status (HARUS DI ATAS {type})
        Route::get('/{id}/status', [DocumentController::class, 'getStatus'])
            ->where('id', '[0-9]+')  // ⬅️ Hanya accept angka
            ->name('getStatus');
        
        // ✅ Detail dokumen (HARUS DI ATAS {type})
        Route::get('/{id}/detail', [DocumentController::class, 'detail'])
            ->where('id', '[0-9]+')  // ⬅️ Hanya accept angka
            ->name('detail');
        
        // ✅ Retry dokumen yang failed
        Route::post('/{id}/retry', [UploadController::class, 'retry'])
            ->where('id', '[0-9]+')
            ->name('retry');
        
        // Delete dokumen
        Route::delete('/{id}', [DocumentController::class, 'destroy'])
            ->where('id', '[0-9]+')
            ->name('destroy');
        
        // ⬇️ ROUTE GENERAL DI BAWAH
        
        // Filter berdasarkan tipe (DI PALING BAWAH karena paling general)
        Route::get('/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'pdf|gambar|doc')
            ->name('filter');
    });

    // Form Checklist
    Route::prefix('form-checklist')->group(function () {
        Route::post('/process/{uploadId}', [FormChecklistController::class, 'processUpload']);
        Route::get('/wireline/{idFcw}', [FormChecklistController::class, 'getWireline']);
        Route::get('/wireless/{idFcwl}', [FormChecklistController::class, 'getWireless']);
    });

    // Chatbot Page
    Route::get('/test', function() {
        return Inertia::render('chatbot');
    })->name('chatbot');

    // ✅ CHATBOT API ENDPOINTS (RAG System)
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        // Main chat endpoint dengan RAG
        Route::post('/chat', [ChatbotController::class, 'chat'])->name('chat');
        
        // Generate embedding (untuk testing)
        Route::post('/generate-embedding', [ChatbotController::class, 'generateEmbedding'])
            ->name('generate.embedding');
        
        // Health check
        Route::get('/health', [ChatbotController::class, 'health'])->name('health');
        
        // Statistics
        Route::get('/stats', [ChatbotController::class, 'stats'])->name('stats');
        
        // Legacy endpoints (jika masih dipakai)
        Route::post('/message', [ChatbotController::class, 'sendMessage'])->name('message');
        Route::post('/stream', [ChatbotController::class, 'sendMessageStream'])->name('stream');
    });

    // Send to Python
    Route::post('/send-to-python', [DocumentController::class, 'sendToPython'])
        ->name('send.to.python');
        
    // Test Ollama
    Route::get('/test-ollama', function() {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(600)
                ->post('http://localhost:11434/api/generate', [
                    'model' => 'phi3:mini',
                    'prompt' => 'Hello',
                    'stream' => false
                ]);
            
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });

    // Users Page
    Route::get('/users', function () {
        return Inertia::render('users');
    })->name('users'); 
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 📚 ROUTING GUIDE
|--------------------------------------------------------------------------
|
| ========================================
| DOCUMENTS
| ========================================
| POST /documents/pdf                → Upload PDF (background job)
| POST /documents/image              → Upload gambar
| POST /documents/doc                → Upload DOC/DOCX
| GET  /documents/{type}             → Filter (pdf/doc/gambar)
| GET  /documents/{id}/detail        → Detail dokumen
| GET  /documents/{id}/status        → API polling status (NEW!)
| POST /documents/{id}/retry         → Retry dokumen failed (NEW!)
| DELETE /documents/{id}             → Hapus dokumen
|
| ========================================
| CHATBOT API (RAG System)
| ========================================
| POST /chatbot/chat                 → Chat dengan RAG
| GET  /chatbot/health               → Health check
| GET  /chatbot/stats                → Statistics
| POST /chatbot/generate-embedding   → Generate embedding (testing)
|
*/