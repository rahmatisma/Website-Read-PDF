<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ TAMBAHKAN INI:
        Event::listen(
            \App\Events\SPKDataSaved::class,
            \App\Listeners\GenerateEmbedding::class,
        );
    }
}