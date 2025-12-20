<?php

namespace App\Listeners;

use App\Events\SPKDataSaved;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Log;
use Exception;

class GenerateEmbedding
{
    protected EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    public function handle(SPKDataSaved $event): void
    {
        try {
            Log::info('Starting embedding generation', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan
            ]);

            // GENERATE SPK EMBEDDING SAJA (sudah include semua info jaringan)
            $this->embeddingService->generateSpkEmbedding($event->idSpk);

            Log::info('Embedding generation completed', [
                'id_spk' => $event->idSpk
            ]);

        } catch (Exception $e) {
            Log::error('Failed to generate embedding', [
                'id_spk' => $event->idSpk,
                'error' => $e->getMessage()
            ]);
        }
    }
}