<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FormChecklistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DokumenSearchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ========================================
// PUBLIC ROUTES
// ========================================
Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');

// ========================================
// AUTHENTICATED ROUTES
// ========================================
Route::middleware(['auth', 'verified.admin'])->group(function () {

    // ========================================
    // DASHBOARD
    // ========================================
    Route::get('/dashboard', [DocumentController::class, 'dashboard'])->name('dashboard');

    // ========================================
    // DOCUMENTS ROUTES
    // ========================================
    Route::prefix('documents')->name('documents.')->group(function () {
        // Main documents page - redirect to SPK filter
        Route::get('/', function() {
            return redirect()->route('documents.filter', ['type' => 'spk']);
        })->name('index');

        // Upload endpoints
        Route::post('/spk', [UploadController::class, 'storeSPK'])->name('store.spk');
        Route::post('/checklist', [UploadController::class, 'storeChecklist'])->name('store.checklist');

        // Search routes
        Route::get('/search', [DokumenSearchController::class, 'index'])->name('search');
        Route::get('/search/api/search', [DokumenSearchController::class, 'search']);
        Route::get('/search/api/filter-options', [DokumenSearchController::class, 'getFilterOptions']);
        Route::get('/search/api/customer-summary', [DokumenSearchController::class, 'getCustomerSummary']);

        // Detail & Actions (specific routes before wildcard)
        Route::get('/detail/{id}', [DocumentController::class, 'detail'])
            ->where('id', '[0-9]+')
            ->name('detail');
        Route::post('/retry/{id}', [UploadController::class, 'retry'])
            ->where('id', '[0-9]+')
            ->name('retry');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])
            ->where('id', '[0-9]+')
            ->name('destroy');

        // Filter by type (MUST BE LAST - catch-all route)
        Route::get('/filter/{type}', [DocumentController::class, 'filter'])
            ->where('type', 'spk|form-checklist|form-pm-pop') // ✅ Update regex
            ->name('filter');
    });

    // ========================================
    // SMART SEARCH SYSTEM (NEW)
    // ========================================
    Route::prefix('search')->name('search.')->group(function () {
        // Main search page
        Route::get('/', [DokumenSearchController::class, 'index'])->name('index');
        
        // API Endpoints (AJAX)
        Route::get('/api/search', [DokumenSearchController::class, 'search'])->name('api.search');
        Route::get('/api/filter-options', [DokumenSearchController::class, 'getFilterOptions'])->name('api.filter-options');
        Route::get('/api/customer-summary', [DokumenSearchController::class, 'getCustomerSummary'])->name('api.customer-summary');
        Route::get('/api/stats', [DokumenSearchController::class, 'getQuickStats'])->name('api.stats');
        Route::get('/api/export', [DokumenSearchController::class, 'export'])->name('api.export');
    });

    // ========================================
    // FORM CHECKLIST
    // ========================================
    Route::prefix('form-checklist')->name('form-checklist.')->group(function () {
        Route::post('/process/{uploadId}', [FormChecklistController::class, 'processUpload'])->name('process');
        Route::get('/wireline/{idFcw}', [FormChecklistController::class, 'getWireline'])->name('wireline');
        Route::get('/wireless/{idFcwl}', [FormChecklistController::class, 'getWireless'])->name('wireless');
    });

    // ========================================
    // CHATBOT (Legacy)
    // ========================================
    Route::get('/test', function() {
        return Inertia::render('chatbot');
    })->name('chatbot');

    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/chat', [ChatbotController::class, 'chat'])->name('chat');
        Route::post('/generate-embedding', [ChatbotController::class, 'generateEmbedding'])->name('generate.embedding');
        Route::get('/health', [ChatbotController::class, 'health'])->name('health');
        Route::post('/chat-stream', [ChatbotController::class, 'chatStream'])->name('chat.stream');
        Route::get('/stats', [ChatbotController::class, 'stats'])->name('stats');
        Route::post('/message', [ChatbotController::class, 'sendMessage'])->name('message');
        Route::post('/stream', [ChatbotController::class, 'sendMessageStream'])->name('stream');
    });

    // ========================================
    // API ENDPOINTS (for AJAX/Polling)
    // ========================================
    Route::prefix('api')->name('api.')->group(function () {
        // Documents
        Route::post('/documents/check-status', [DocumentController::class, 'checkStatus'])->name('documents.checkStatus');
        Route::get('/documents/{id}/status', [DocumentController::class, 'getStatus'])
            ->where('id', '[0-9]+')
            ->name('documents.getStatus');
        
        // Dashboard
        Route::get('/dashboard/stats', [DocumentController::class, 'getDashboardStats'])->name('dashboard.stats');
    });

    // ========================================
    // PYTHON INTEGRATION
    // ========================================
    Route::post('/send-to-python', [DocumentController::class, 'sendToPython'])->name('send.to.python');

    // ========================================
    // TEST ENDPOINTS
    // ========================================
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
    })->name('test.ollama');

    // ========================================
    // USER MANAGEMENT (ADMIN ONLY)
    // ========================================
    Route::prefix('users')
        ->name('users.')
        ->middleware(['admin.only'])
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-admin-verification', [UserController::class, 'toggleAdminVerification'])->name('toggleAdminVerification');
        });
});

// ========================================
// ADDITIONAL ROUTE FILES
// ========================================
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';