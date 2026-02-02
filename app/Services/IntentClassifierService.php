<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * 🧠 Intent Classifier Service - SIMPLIFIED
 * 
 * Flask app.py sudah handle semua logic, service ini hanya wrapper
 */
class IntentClassifierService
{
    protected string $flaskApiUrl;

    public function __construct()
    {
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
    }

    /**
     * Classify user intent (delegasi ke Flask)
     * 
     * @param string $query User query
     * @param array $context Current conversation context
     * @return array Intent classification result
     */
    public function classify(string $query, array $context = []): array
    {
        try {
            Log::info('🧠 Intent Classification Started', [
                'query' => $query,
                'context' => $context
            ]);

            // Ensure context is proper format (TIDAK BERUBAH)
            if (empty($context)) {
                $context = [
                    'last_nojar' => null,
                    'last_spk' => null,
                    'last_pelanggan' => null,
                ];
            }

            // Call Flask /classify-intent (TIDAK BERUBAH)
            $response = Http::timeout(60)->post("{$this->flaskApiUrl}/classify-intent", [
                'query' => $query,
                'context' => $context,
            ]);

            if (!$response->successful()) {
                throw new Exception("Flask API error: " . $response->body());
            }

            $result = $response->json();

            //  PERUBAHAN DI SINI 
            // Extract entities dari Flask (Flask return dengan key 'nojar', 'spk')
            $entitiesFromFlask = $result['entities'] ?? [];
            
            // Convert ke format yang konsisten dengan context
            $normalizedEntities = [
                'last_nojar' => $entitiesFromFlask['nojar'] ?? ($context['last_nojar'] ?? null),
                'last_spk' => $entitiesFromFlask['spk'] ?? ($context['last_spk'] ?? null),
                'last_pelanggan' => $context['last_pelanggan'] ?? null,  // Keep from context
            ];

            //  Log jika ada entity baru yang di-extract
            if (!empty($entitiesFromFlask)) {
                Log::info('📝 Entities extracted by Flask', [
                    'raw_from_flask' => $entitiesFromFlask,
                    'normalized' => $normalizedEntities
                ]);
            }
            //  END OF CHANGES 

            Log::info(' Intent Classified', [
                'type' => $result['type'] ?? 'UNKNOWN',
                'strategy' => $result['strategy'] ?? 'RAG',
                'confidence' => $result['confidence'] ?? 0.5,
                'rule_match' => $result['rule_match'] ?? false
            ]);

            return [
                'type' => $result['type'] ?? 'GENERAL_INFO',
                'strategy' => $result['strategy'] ?? 'RAG',
                'confidence' => $result['confidence'] ?? 0.6,
                'reasoning' => $result['reasoning'] ?? '',
                'entities' => $normalizedEntities,  //  UBAH: Return normalized entities
                'rule_match' => $result['rule_match'] ?? false,
            ];

        } catch (Exception $e) {
            Log::error('Intent Classification Failed', [
                'error' => $e->getMessage()
            ]);

            // Fallback (TIDAK BERUBAH)
            return $this->fallbackClassification($query, $context);
        }
    }

    /**
     * Fallback classification (jika Flask down)
     */
    private function fallbackClassification(string $query, array $context): array
    {
        $queryLower = strtolower($query);
        $hasContext = !empty($context['last_nojar']) || !empty($context['last_spk']);

        // Simple rule-based fallback
        $sqlKeywords = ['berapa', 'ada', 'siapa', 'kapan', 'dimana', 'detail', 'list'];
        $isSqlQuery = false;
        
        foreach ($sqlKeywords as $keyword) {
            if (str_contains($queryLower, $keyword)) {
                $isSqlQuery = true;
                break;
            }
        }

        if ($isSqlQuery && $hasContext) {
            $strategy = 'SQL';
            $type = 'SPECIFIC_QUERY';
        } else {
            $strategy = 'RAG';
            $type = 'GENERAL_INFO';
        }

        Log::warning('⚠️ Using fallback classification', [
            'strategy' => $strategy,
            'type' => $type
        ]);

        return [
            'type' => $type,
            'strategy' => $strategy,
            'entities' => [
                'nojar' => $context['last_nojar'] ?? null,
                'spk' => $context['last_spk'] ?? null,
            ],
            'confidence' => 0.5,
            'reason' => 'Fallback classification (Flask unavailable)',
            'is_fallback' => true,
        ];
    }
}