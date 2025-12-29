<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\EmbeddingService;
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

    public function __construct(
        ChatbotService $chatbotService,
        EmbeddingService $embeddingService
    ) {
        $this->chatbotService = $chatbotService;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Chat dengan chatbot (NON-STREAMING) - untuk RAG mode
     */
    public function chat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:500',
            'conversation_history' => 'nullable|array',
            'current_context' => 'nullable|array',
            'search_type' => 'nullable|in:jaringan,spk,both',
            'top_k' => 'nullable|integer|min:1|max:10',
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

            $options = [
                'search_type' => $request->input('search_type', 'both'),
                'top_k' => $request->input('top_k', 3),
                'min_similarity' => $request->input('min_similarity', 0.5),
            ];

            $result = $this->chatbotService->chat(
                $query,
                $conversationHistory,
                $currentContext,
                $options
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Gagal generate jawaban',
                ], 200);
            }

            $responseData = [
                'query' => $result['query'] ?? $query,
                'enhanced_query' => $result['enhanced_query'] ?? null,
                'answer' => $result['answer'] ?? 'Tidak ada jawaban.',
                'source' => $result['source'] ?? 'unknown',
                'query_type' => $result['query_type'] ?? null,
                'relevant_data_count' => isset($result['relevant_data'])
                    ? count($result['relevant_data'])
                    : 0,
                'relevant_data' => $result['relevant_data'] ?? [],
                'extracted_entities' => $result['extracted_entities'] ?? [],
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ], 200);

        } catch (Exception $e) {
            Log::error('Chat controller error', [
                'error' => $e->getMessage(),
                'query' => $request->input('query'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * 🔥 NEW: STREAMING chat dengan Ollama
     *
     * Request Body:
     * {
     *   "query": "Pertanyaan user",
     *   "conversation_history": [...],
     *   "current_context": {...}
     * }
     *
     * Response: Server-Sent Events (SSE) streaming
     */
    public function chatStream(Request $request): StreamedResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:500',
            'conversation_history' => 'nullable|array',
            'current_context' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->streamJson([
                'error' => 'Validasi gagal: ' . $validator->errors()->first(),
            ], 400);
        }

        $query = $request->input('query');
        $conversationHistory = $request->input('conversation_history', []);
        $currentContext = $request->input('current_context', []);

        $flaskUrl = env('FLASK_API_URL', 'http://localhost:5000');

        Log::info('🌊 Streaming chat request', [
            'query' => $query,
            'has_history' => !empty($conversationHistory),
            'has_context' => !empty($currentContext),
        ]);

        return response()->stream(function () use ($flaskUrl, $query, $conversationHistory, $currentContext) {
            try {
                // ✅ Build conversation history untuk Ollama
                $historyForOllama = [];
                foreach ($conversationHistory as $msg) {
                    $historyForOllama[] = [
                        'role' => $msg['role'] ?? 'user',
                        'content' => $msg['content'] ?? '',
                    ];
                }

                // ✅ Build context string (optional - bisa dikosongkan jika pure streaming)
                $contextString = '';
                if (!empty($currentContext)) {
                    $contextParts = [];
                    if (isset($currentContext['last_nojar'])) {
                        $contextParts[] = "Nojar: {$currentContext['last_nojar']}";
                    }
                    if (isset($currentContext['last_pelanggan'])) {
                        $contextParts[] = "Pelanggan: {$currentContext['last_pelanggan']}";
                    }
                    $contextString = implode("\n", $contextParts);
                }

                // ✅ Call Flask streaming endpoint
                $ch = curl_init("{$flaskUrl}/chat-stream");

                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode([
                        'query' => $query,
                        'context' => $contextString,
                        'conversation_history' => $historyForOllama,
                        'model' => env('OLLAMA_CHAT_MODEL', 'llama3.2:3b'),
                    ]),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                    ],
                    CURLOPT_RETURNTRANSFER => false,
                    CURLOPT_WRITEFUNCTION => function ($ch, $data) {
                        // ✅ Forward setiap chunk dari Flask langsung ke client
                        echo $data;

                        // Flush output buffer agar langsung terkirim
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
     * Generate embedding untuk teks tertentu (untuk testing)
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
}
