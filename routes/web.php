<?php

use App\Http\Controllers\UploadController;
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
        Route::post('/pdf',    [UploadController::class, 'storePDF'])->name('store.pdf');
        Route::post('/image',  [UploadController::class, 'storeImage'])->name('store.image');
        Route::post('/doc',    [UploadController::class, 'storeDoc'])->name('store.doc');

        // Filter berdasarkan tipe
        Route::get('/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'pdf|gambar|doc')
            ->name('filter');
    });

    // Chatbot Test
    Route::get('/test', function() {
        return Inertia::render('chatbot');
    })->name('chatbot');

    // Endpoint untuk Chatbot
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name(
        'chatbot.message'
    );


    // 🔥 Tambahkan route untuk kirim file ke Python Flask
    Route::post('/send-to-python', [DocumentController::class, 'sendToPython'])
        ->name('send.to.python');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
