<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ChatbotController;
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

    // Chatbot Page
    Route::get('/test', function() {
        return Inertia::render('chatbot');
    })->name('chatbot');

    // Chatbot API Endpoint - DIPINDAHKAN KE SINI
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])
        ->name('chatbot.message');

    // Send to Python
    Route::post('/send-to-python', [DocumentController::class, 'sendToPython'])
        ->name('send.to.python');
        
    // Test Ollama
    Route::get('/test-ollama', function() {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
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