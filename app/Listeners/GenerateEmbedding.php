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

            // Generate embedding untuk JARINGAN
            $this->embeddingService->generateJaringanEmbedding($event->noJaringan);
            
            // Generate embedding untuk SPK
            $this->embeddingService->generateSpkEmbedding($event->idSpk);

            Log::info('Embedding generation completed', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan
            ]);

        } catch (Exception $e) {
            Log::error('Failed to generate embedding', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }
}