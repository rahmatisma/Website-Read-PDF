<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
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
     * POST /api/chatbot/chat
     * Body: {
     *   "query": "Cek nojar 12345 untuk pelanggan siapa?",
     *   "search_type": "both",  // optional: "jaringan", "spk", "both"
     *   "top_k": 3              // optional: jumlah data relevan
     * }
     */
    public function chat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:500',
            'search_type' => 'nullable|in:jaringan,spk,both',
            'top_k' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = $request->input('query');
            $options = [
                'search_type' => $request->input('search_type', 'both'),
                'top_k' => $request->input('top_k', 3),
            ];

            $result = $this->chatbotService->chat($query, $options);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate jawaban',
                    'error' => $result['error'] ?? 'Unknown error',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $result['query'],
                    'answer' => $result['answer'],
                    'relevant_data_count' => count($result['relevant_data']),
                    'relevant_data' => $result['relevant_data'],
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada chatbot',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate embedding untuk teks tertentu (untuk testing)
     * 
     * POST /api/chatbot/generate-embedding
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
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
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
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate embedding',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Health check untuk chatbot
     * 
     * GET /api/chatbot/health
     */
    public function health(): JsonResponse
    {
        try {
            // Test koneksi ke Flask API
            $flaskUrl = env('FLASK_API_URL', 'http://localhost:5000');
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("{$flaskUrl}/health");

            $flaskStatus = $response->successful() ? 'online' : 'offline';

        } catch (Exception $e) {
            $flaskStatus = 'offline';
        }

        return response()->json([
            'success' => true,
            'service' => 'Chatbot Service',
            'status' => 'online',
            'flask_api' => [
                'url' => env('FLASK_API_URL', 'http://localhost:5000'),
                'status' => $flaskStatus,
            ],
            'embedding' => [
                'model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
                'dimension' => env('EMBEDDING_DIMENSION', 384),
            ],
        ]);
    }

    /**
     * Get statistik embeddings
     * 
     * GET /api/chatbot/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $jaringanCount = \App\Models\JaringanEmbedding::count();
            $spkCount = \App\Models\SpkEmbedding::count();
            $totalCount = $jaringanCount + $spkCount;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_embeddings' => $totalCount,
                    'jaringan_embeddings' => $jaringanCount,
                    'spk_embeddings' => $spkCount,
                    'embedding_model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
                    'embedding_dimension' => env('EMBEDDING_DIMENSION', 384),
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal get stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}