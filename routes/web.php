<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FormChecklistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        // Upload dokumen
        Route::post('/pdf', 'App\Http\Controllers\UploadController@storePDF')->name('store.pdf');
        Route::post('/image', 'App\Http\Controllers\UploadController@storeImage')->name('store.image');
        Route::post('/doc', 'App\Http\Controllers\UploadController@storeDoc')->name('store.doc');
        
        // Delete dokumen
        Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
        
        // Filter berdasarkan tipe
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
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Cara Pakai Chatbot API:
|--------------------------------------------------------------------------
|
| 1. Chat dengan RAG:
|    POST /chatbot/chat
|    Body: {
|      "query": "Cek nojar 12345 untuk pelanggan siapa?",
|      "search_type": "both",  // optional: jaringan, spk, both
|      "top_k": 3              // optional: jumlah data relevan
|    }
|
| 2. Health Check:
|    GET /chatbot/health
|
| 3. Statistics:
|    GET /chatbot/stats
|
| 4. Generate Embedding (Testing):
|    POST /chatbot/generate-embedding
|    Body: {"text": "Test text"}
|
*/