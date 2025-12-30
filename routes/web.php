<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FormChecklistController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth', 'verified.admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DocumentController::class, 'dashboard'])->name('dashboard');

    // ========================================
    // DOCUMENTS ROUTES
    // ========================================
    Route::prefix('documents')->name('documents.')->group(function () {
        // ✅ TAMBAHKAN: Route untuk halaman utama documents
        Route::get('/', [DocumentController::class, 'index'])->name('index');

        // Upload dokumen
        Route::post('/pdf', [UploadController::class, 'storePDF'])->name('store.pdf');
        Route::post('/checklist', [UploadController::class, 'storeChecklist'])->name('store.checklist');

        // Detail dokumen
        Route::get('/{id}/detail', [DocumentController::class, 'detail'])
            ->where('id', '[0-9]+')
            ->name('detail');

        // Retry dokumen yang failed
        Route::post('/{id}/retry', [UploadController::class, 'retry'])
            ->where('id', '[0-9]+')
            ->name('retry');

        // Delete dokumen
        Route::delete('/{id}', [DocumentController::class, 'destroy'])
            ->where('id', '[0-9]+')
            ->name('destroy');

        // Filter berdasarkan tipe (harus di paling bawah)
        Route::get('/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'pdf|form-checklist')
            ->name('filter');
    });

    // ========================================
    // API ROUTES untuk AJAX/Polling
    // ========================================
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/documents/check-status', [DocumentController::class, 'checkStatus'])
            ->name('documents.checkStatus');

        Route::get('/dashboard/stats', [DocumentController::class, 'getDashboardStats'])
            ->name('dashboard.stats');

        Route::get('/documents/{id}/status', [DocumentController::class, 'getStatus'])
            ->where('id', '[0-9]+')
            ->name('documents.getStatus');
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

    // ========================================
    // CHATBOT API ENDPOINTS (RAG System)
    // ========================================
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'chat'])->name('chat');
        Route::post('/generate-embedding', [ChatbotController::class, 'generateEmbedding'])
            ->name('generate.embedding');
        Route::get('/health', [ChatbotController::class, 'health'])->name('health');
        Route::post('/chat-stream', [ChatbotController::class, 'chatStream'])->name('chat.stream');
        Route::get('/stats', [ChatbotController::class, 'stats'])->name('stats');
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

    // ========================================
    // USERS MANAGEMENT ROUTES (ADMIN ONLY)
    // ========================================
    Route::prefix('users')
        ->name('users.')
        ->middleware(['admin.only'])
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-admin-verification',
                [UserController::class, 'toggleAdminVerification']
            )->name('toggleAdminVerification');
        });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
