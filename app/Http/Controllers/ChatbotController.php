<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;
    protected EmbeddingService $embeddingService;

    public function __construct(
        ChatbotService $chatbotService,
        EmbeddingService $embeddingService
    ) {
        $this->chatbotService = $chatbotService;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Chat dengan chatbot
     * 
     * POST /chatbot/chat
     * Body: {
     *   "query": "Cek nojar 12345 untuk pelanggan siapa?",
     *   "search_type": "both",  // optional: "jaringan", "spk", "both"
     *   "top_k": 3              // optional: jumlah data relevan
     * }
     */
    public function chat(Request $request): JsonResponse
    {
        // ================================
        // VALIDATION
        // ================================
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:500',
            'conversation_history' => 'nullable|array',
            'conversation_history.*.role' => 'nullable|in:user,assistant',
            'conversation_history.*.content' => 'nullable|string',
            'current_context' => 'nullable|array',
            'current_context.last_nojar' => 'nullable|string',
            'current_context.last_pelanggan' => 'nullable|string',
            'current_context.last_spk' => 'nullable|string',
            'current_context.last_pop' => 'nullable|string',
            'search_type' => 'nullable|in:jaringan,spk,both',
            'top_k' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            Log::warning('Chat validation failed', ['errors' => $validator->errors()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal: ' . $validator->errors()->first(),
            ], 200);
        }

        try {
            // ================================
            // EXTRACT REQUEST DATA
            // ================================
            $query = $request->input('query');
            $conversationHistory = $request->input('conversation_history', []);
            $currentContext = $request->input('current_context', []);
            
            $options = [
                'search_type' => $request->input('search_type', 'both'),
                'top_k' => $request->input('top_k', 3),
                'min_similarity' => $request->input('min_similarity', 0.5),
            ];

            Log::info('Chat request received', [
                'query' => $query,
                'has_history' => !empty($conversationHistory),
                'history_count' => count($conversationHistory),
                'has_context' => !empty($currentContext),
                'context' => $currentContext,
                'options' => $options
            ]);

            // ================================
            // CALL CHATBOT SERVICE
            // ================================
            $result = $this->chatbotService->chat(
                $query,
                $conversationHistory,  // ✅ Pass conversation history
                $currentContext,       // ✅ Pass current context
                $options
            );

            Log::info('Chat service result', [
                'success' => $result['success'],
                'source' => $result['source'] ?? 'unknown',
                'has_answer' => isset($result['answer']),
                'has_extracted_entities' => isset($result['extracted_entities']),
            ]);

            // ================================
            // HANDLE SERVICE FAILURE
            // ================================
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Gagal generate jawaban',
                ], 200);
            }

            // ================================
            // BUILD SUCCESS RESPONSE
            // ================================
            $responseData = [
                'query' => $result['query'] ?? $query,
                'enhanced_query' => $result['enhanced_query'] ?? null,  // ✅ Query yang sudah di-enhance
                'answer' => $result['answer'] ?? 'Tidak ada jawaban.',
                'source' => $result['source'] ?? 'unknown',
                'query_type' => $result['query_type'] ?? null,
                'relevant_data_count' => isset($result['relevant_data']) 
                    ? count($result['relevant_data']) 
                    : 0,
                'relevant_data' => $result['relevant_data'] ?? [],
                'extracted_entities' => $result['extracted_entities'] ?? [],  // ✅ Entities untuk update context
            ];

            // ✅ Log extracted entities jika ada
            if (!empty($result['extracted_entities'])) {
                Log::info('Extracted entities', [
                    'entities' => $result['extracted_entities']
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ], 200);

        } catch (Exception $e) {
            Log::error('Chat controller error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query' => $request->input('query'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Generate embedding untuk teks tertentu (untuk testing)
     * 
     * POST /chatbot/generate-embedding
     * Body: {
     *   "text": "Teks yang ingin di-embed"
     * }
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
     * Health check untuk chatbot
     * 
     * GET /chatbot/health
     */
    public function health(): JsonResponse
    {
        try {
            // Test koneksi ke Flask API
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
            'flask_api' => [
                'url' => $flaskUrl,
                'status' => $flaskStatus,
                'data' => $flaskData,
            ],
            'embedding' => [
                'model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
                'dimension' => env('EMBEDDING_DIMENSION', 384),
            ],
        ], 200);
    }

    /**
     * Get statistik embeddings
     * 
     * GET /chatbot/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $spkCount = \App\Models\SpkEmbedding::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_embeddings' => $spkCount,
                    'spk_embeddings' => $spkCount,
                    'embedding_model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
                    'embedding_dimension' => env('EMBEDDING_DIMENSION', 384),
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
     * Legacy: Send message (redirect ke chat)
     */
    public function sendMessage(Request $request): JsonResponse
    {
        return $this->chat($request);
    }

    /**
     * Legacy: Streaming endpoint (deprecated)
     */
    public function sendMessageStream(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Streaming endpoint deprecated. Use /chatbot/chat instead.',
        ], 200);
    }
}