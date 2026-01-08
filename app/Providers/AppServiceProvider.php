<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Services\EmbeddingService;
use App\Services\IntentClassifierService;
use App\Services\SqlGeneratorService;
use App\Services\AnswerValidatorService;
use App\Services\ChatbotService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register EmbeddingService (singleton)
        $this->app->singleton(EmbeddingService::class, function ($app) {
            return new EmbeddingService();
        });

        // Register IntentClassifierService (singleton)
        $this->app->singleton(IntentClassifierService::class, function ($app) {
            return new IntentClassifierService();
        });

        // Register SqlGeneratorService (singleton)
        $this->app->singleton(SqlGeneratorService::class, function ($app) {
            return new SqlGeneratorService();
        });

        // Register ChatbotService (singleton) with dependencies
        $this->app->singleton(ChatbotService::class, function ($app) {
            return new ChatbotService(
                $app->make(EmbeddingService::class),
                $app->make(IntentClassifierService::class),
                $app->make(SqlGeneratorService::class)
            );
        });

        $this->app->singleton(AnswerValidatorService::class);
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