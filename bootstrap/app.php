<?php

use App\Http\Middleware\EnsureUserIsVerified;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude cookies dari enkripsi
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        // CSRF exception untuk chatbot streaming
        $middleware->validateCsrfTokens(except: [
            'chatbot/stream',
            'chatbot/chat',
        ]);

        // ✅ TAMBAHAN: Register middleware alias untuk admin verification
        $middleware->alias([
            'verified.admin' => EnsureUserIsVerified::class,
            'admin.only'     => \App\Http\Middleware\AdminOnly::class,
        ]);

        // Web middleware group
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();