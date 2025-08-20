<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Documents
    Route::prefix('documents')->group(function () {
        // tampilkan halaman documents/index (dengan tab PDF/Gambar/Doc)
        Route::get('/', [UploadController::class, 'index'])->name('documents.index');

        // upload PDF
        Route::post('/', [UploadController::class, 'store'])->name('documents.store');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
