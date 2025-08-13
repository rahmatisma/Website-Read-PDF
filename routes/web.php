<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use PhpParser\Comment\Doc;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    Route::get('/documents', [DocumentController::class, 'Index'])->name('document.index');
});

Route::get('/documents/{tab?}', function ($tab = 'pdf') {
    return Inertia::render('Documents/Index', ['tab' => $tab]);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
