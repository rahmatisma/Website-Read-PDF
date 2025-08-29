<?php

use App\Http\Controllers\UploadController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        // Upload berdasarkan tipe
        Route::post('/pdf', [UploadController::class, 'storePDF'])->name('store.pdf');
        Route::post('/image', [UploadController::class, 'storeImage'])->name('store.image');
        Route::post('/doc', [UploadController::class, 'storeDoc'])->name('store.doc');

        // Filter dokumen berdasarkan tipe
        Route::get('/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'pdf|gambar|doc')
            ->name('filter');
    });

    Route::get('/dashboard', [DocumentController::class, 'dashboard'])->name('dashboard');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
