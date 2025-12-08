<?php

use App\Http\Controllers\DocumentController;
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
        Route::post('/pdf',    'App\Http\Controllers\UploadController@storePDF')->name('store.pdf');
        Route::post('/image',  'App\Http\Controllers\UploadController@storeImage')->name('store.image');
        Route::post('/doc',    'App\Http\Controllers\UploadController@storeDoc')->name('store.doc');
        
        // Delete dokumen - TAMBAHKAN INI
        Route::delete('/{id}', 'App\Http\Controllers\DocumentController@destroy')->name('destroy');
        
        // Filter berdasarkan tipe
        Route::get('/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'pdf|gambar|doc')
            ->name('filter');
    });


    Route::post('/send-to-python', [DocumentController::class, 'sendToPython'])
        ->name('send.to.python');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';