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
            Log::info('🚀 Starting embedding generation', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan
            ]);

            // ✅ GENERATE JARINGAN EMBEDDING DULU
            try {
                $this->embeddingService->generateJaringanEmbedding($event->noJaringan);
                Log::info('✅ Jaringan embedding generated', [
                    'no_jaringan' => $event->noJaringan
                ]);
            } catch (Exception $e) {
                // Jaringan mungkin sudah ada embedding, skip error
                Log::warning('⚠️ Jaringan embedding failed (might already exist)', [
                    'no_jaringan' => $event->noJaringan,
                    'error' => $e->getMessage()
                ]);
            }

            // ✅ GENERATE SPK EMBEDDING
            $this->embeddingService->generateSpkEmbedding($event->idSpk);
            Log::info('✅ SPK embedding generated', [
                'id_spk' => $event->idSpk
            ]);

            Log::info('🎉 All embeddings generation completed', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan
            ]);

        } catch (Exception $e) {
            Log::error('❌ Failed to generate embedding', [
                'id_spk' => $event->idSpk,
                'no_jaringan' => $event->noJaringan,
                'error' => $e->getMessage()
            ]);
            
            // ❌ JANGAN throw exception supaya upload tidak gagal
            // throw $e;
        }
    }
}