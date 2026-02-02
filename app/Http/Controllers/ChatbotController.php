<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\EmbeddingService;
use App\Services\AnswerValidatorService;  //  NEW
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;
    protected EmbeddingService $embeddingService;
    protected AnswerValidatorService $answerValidator;  //  NEW

    public function __construct(
        ChatbotService $chatbotService,
        EmbeddingService $embeddingService,
        AnswerValidatorService $answerValidator  //  NEW
    ) {
        $this->chatbotService = $chatbotService;
        $this->embeddingService = $embeddingService;
        $this->answerValidator = $answerValidator;  //  NEW
    }

    /**
     * Chat dengan chatbot (NON-STREAMING)
     */
    public function chat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:500',
            'conversation_history' => 'nullable|array',
            'current_context' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal: ' . $validator->errors()->first(),
            ], 200);
        }

        try {
            $query = $request->input('query');
            $conversationHistory = $request->input('conversation_history', []);
            $currentContext = $request->input('current_context', []);

            //  FIX: Extract entities dari query SEBELUM classify 
            $extractedFromQuery = $this->quickExtractEntities($query);
            
            // Override context dengan entities dari query
            if (!empty($extractedFromQuery['nojar'])) {
                $oldNojar = $currentContext['last_nojar'] ?? null;
                $currentContext['last_nojar'] = $extractedFromQuery['nojar'];
                
                if ($oldNojar !== $extractedFromQuery['nojar']) {
                    Log::info('🔄 Context override (nojar from query)', [
                        'old' => $oldNojar,
                        'new' => $extractedFromQuery['nojar']
                    ]);
                    
                    // Clear related context
                    $currentContext['last_spk'] = null;
                    $currentContext['last_pelanggan'] = null;
                }
            }
            
            if (!empty($extractedFromQuery['spk'])) {
                $currentContext['last_spk'] = $extractedFromQuery['spk'];
            }
            //  END OF FIX 

            Log::info('🤖 Chat request received', [
                'query' => $query,
                'context_before_chat' => $currentContext,  //  Log context yang sudah di-update
            ]);

            // 🚀 Call ChatbotService (sudah ada validator di dalamnya)
            $result = $this->chatbotService->chat(
                $query,
                $conversationHistory,
                $currentContext,
                []
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Gagal generate jawaban',
                ], 200);
            }

            Log::info(' Chat response generated', [
                'strategy' => $result['strategy'] ?? 'unknown',
                'source' => $result['source'] ?? 'unknown',
                'validation' => $result['validation'] ?? 'not_checked',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $result['query'],
                    'answer' => $result['answer'],
                    'source' => $result['source'],
                    'strategy' => $result['strategy'] ?? 'unknown',
                    'query_type' => $result['query_type'] ?? null,
                    'extracted_entities' => $result['extracted_entities'] ?? [],
                    'validation' => $result['validation'] ?? 'passed',  //  NEW
                ],
            ], 200);

        } catch (Exception $e) {
            Log::error('Chat controller error', [
                'error' => $e->getMessage(),
                'query' => $request->input('query'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     *  NEW: Quick entity extraction (simple regex, no LLM)
     */
    private function quickExtractEntities(string $query): array
    {
        $entities = [];
        
        // Extract nojar (10 digits)
        if (preg_match('/\b(\d{10})\b/', $query, $match)) {
            $entities['nojar'] = $match[1];
        }
        
        // Extract SPK
        $spkPatterns = [
            '/spk\s+([0-9]{6}\/[A-Z\-]+\/\d{4})/i',
            '/no\s+spk\s+([0-9]{6}\/[A-Z\-]+\/\d{4})/i',
            '/([0-9]{6}\/[A-Z\-]+\/\d{4})/i',
        ];
        
        foreach ($spkPatterns as $pattern) {
            if (preg_match($pattern, $query, $match)) {
                $entities['spk'] = $match[1];
                break;
            }
        }
        
        return $entities;
    }

    /**
     * 🔥 STREAMING chat dengan Ollama
     */
    public function chatStream(Request $request): StreamedResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:1|max:500',
            'conversation_history' => 'nullable|array',
            'current_context' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal: ' . $validator->errors()->first(),
            ], 400);
        }

        $query = $request->input('query');
        $conversationHistory = $request->input('conversation_history', []);
        $currentContext = $request->input('current_context', []);

        Log::info('🌊 Streaming chat request', [
            'query' => $query,
            'has_history' => !empty($conversationHistory),
            'has_context' => !empty($currentContext),
        ]);

        //  ALWAYS TRY NON-STREAMING FIRST (untuk SQL queries)
        try {
            $result = $this->chatbotService->chat(
                $query,
                $conversationHistory,
                $currentContext,
                []
            );

            //  Jika strategy SQL dan berhasil, return direct (no streaming)
            if ($result['success'] && 
                isset($result['strategy']) && 
                $result['strategy'] === 'SQL' &&
                in_array($result['source'], ['direct_sql', 'direct_sql_empty'])) {
                
                Log::info(' Using non-streaming response for SQL query');
                
                return response()->json([
                    'success' => true,
                    'answer' => $result['answer'],
                    'source' => $result['source'],
                    'strategy' => 'SQL',
                    'query_type' => $result['query_type'] ?? 'unknown',
                    'extracted_entities' => $result['extracted_entities'] ?? [],
                    'validation' => $result['validation'] ?? 'passed',
                ])
                ->header('Content-Type', 'application/json')
                ->header('X-Response-Type', 'direct-sql');
            }

        } catch (Exception $e) {
            Log::warning('⚠️ Non-streaming attempt failed, falling back to streaming', [
                'error' => $e->getMessage()
            ]);
        }

        //  CONTINUE WITH STREAMING (for RAG queries)
        $flaskUrl = env('FLASK_API_URL', 'http://localhost:5000');

        return response()->stream(function () use ($flaskUrl, $query, $conversationHistory, $currentContext) {
            try {
                $contextString = '';
                
                try {
                    $queryEmbedding = $this->embeddingService->generateEmbedding($query);
                    
                    $relevantData = $this->chatbotService->contextAwareSimilaritySearch(
                        $queryEmbedding,
                        $currentContext,
                        ['top_k' => 5, 'min_similarity' => 0.4]
                    );
                    
                    if (!empty($relevantData)) {
                        $contextString = $this->buildContextString($relevantData);
                        
                        Log::info('📊 RAG Context built for streaming', [
                            'context_length' => strlen($contextString),
                            'relevant_data_count' => count($relevantData)
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('RAG search failed', ['error' => $e->getMessage()]);
                }
                
                // Build history
                $historyForOllama = [];
                foreach ($conversationHistory as $msg) {
                    $historyForOllama[] = [
                        'role' => $msg['role'] ?? 'user',
                        'content' => $msg['content'] ?? '',
                    ];
                }

                //  Call Flask streaming with STRICT mode
                $ch = curl_init("{$flaskUrl}/chat-stream");

                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode([
                        'query' => $query,
                        'context' => $contextString,
                        'conversation_history' => $historyForOllama,
                        'model' => env('OLLAMA_CHAT_MODEL', 'llama3.1:8b'),
                        'mode' => 'strict',  //  NEW: Force strict mode
                    ]),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_RETURNTRANSFER => false,
                    CURLOPT_WRITEFUNCTION => function ($ch, $data) {
                        echo $data;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                        return strlen($data);
                    },
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_CONNECTTIMEOUT => 10,
                ]);

                $success = curl_exec($ch);

                if ($success === false) {
                    $error = curl_error($ch);
                    Log::error('Streaming curl error', ['error' => $error]);
                    echo "data: " . json_encode(['error' => "Connection error: {$error}"]) . "\n\n";
                    flush();
                }

                curl_close($ch);

            } catch (Exception $e) {
                Log::error('Streaming error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Generate embedding untuk teks
     */
    public function generateEmbedding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal: ' . $validator->errors()->first(),
            ], 200);
        }

        try {
            $text = $request->input('text');
            $embedding = $this->embeddingService->generateEmbedding($text);

            return response()->json([
                'success' => true,
                'data' => [
                    'text' => $text,
                    'embedding_dimension' => count($embedding),
                    'embedding' => $embedding,
                ],
            ], 200);

        } catch (Exception $e) {
            Log::error('Generate embedding error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal generate embedding: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        try {
            $flaskUrl = env('FLASK_API_URL', 'http://localhost:5000');
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("{$flaskUrl}/health");

            $flaskStatus = $response->successful() ? 'online' : 'offline';
            $flaskData = $response->successful() ? $response->json() : null;

        } catch (Exception $e) {
            $flaskStatus = 'offline';
            $flaskData = null;
        }

        return response()->json([
            'success' => true,
            'service' => 'Chatbot Service',
            'status' => 'online',
            'version' => '4.0.0-anti-hallucination',  //  Updated version
            'flask_api' => [
                'url' => $flaskUrl,
                'status' => $flaskStatus,
                'data' => $flaskData,
            ],
            'features' => [
                'intent_classification' => true,
                'sql_generation' => true,
                'rag_search' => true,
                'hybrid_mode' => true,
                'answer_validation' => true,  //  NEW
                'anti_hallucination' => true,  //  NEW
            ],
        ], 200);
    }

    /**
     * Get stats
     */
    public function stats(): JsonResponse
    {
        try {
            $spkCount = \App\Models\SpkEmbedding::count();
            $jaringanCount = \App\Models\JaringanEmbedding::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_embeddings' => $spkCount + $jaringanCount,
                    'spk_embeddings' => $spkCount,
                    'jaringan_embeddings' => $jaringanCount,
                    'embedding_model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
                    'chat_model' => env('OLLAMA_CHAT_MODEL', 'llama3.1:8b'),
                ],
            ], 200);

        } catch (Exception $e) {
            Log::error('Stats error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal get stats: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Build context string dari relevant data
     */
    private function buildContextString(array $relevantData): string
    {
        if (empty($relevantData)) {
            return '';
        }

        $contextParts = ["=== DATA YANG RELEVAN ===\n"];

        foreach ($relevantData as $index => $data) {
            $type = $data['type'] ?? 'unknown';
            $label = strtoupper($type);
            
            $contextParts[] = "\n--- {$label} #" . ($index + 1) . " ---";
            $contextParts[] = "Similarity: " . number_format($data['similarity'] ?? 0, 4);
            $contextParts[] = "\nContent:\n" . ($data['content_text'] ?? '');
            $contextParts[] = "";
        }

        return implode("\n", $contextParts);
    }
}