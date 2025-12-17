<?php

namespace App\Services;

use App\Models\JaringanEmbedding;
use App\Models\SpkEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatbotService
{
    protected EmbeddingService $embeddingService;
    protected string $flaskApiUrl;
    protected int $timeout;
    protected int $topK;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->timeout = env('FLASK_API_TIMEOUT', 300);
        $this->topK = env('CHATBOT_TOP_K', 3); // Ambil top 3 data paling relevan
    }

    /**
     * Chat dengan chatbot
     */
    public function chat(string $query, array $options = []): array
    {
        try {
            Log::info('Chatbot query received', ['query' => $query]);

            // 1. Generate embedding untuk query
            $queryEmbedding = $this->embeddingService->generateEmbedding($query);

            // 2. Similarity search untuk cari data paling relevan
            $relevantData = $this->similaritySearch($queryEmbedding, $options);

            // 3. Build context dari data yang relevan
            $context = $this->buildContext($relevantData);

            // 4. Call Flask API untuk generate jawaban
            $answer = $this->callFlaskChatbot($query, $context);

            Log::info('Chatbot response generated', [
                'query' => $query,
                'relevant_data_count' => count($relevantData)
            ]);

            return [
                'success' => true,
                'query' => $query,
                'answer' => $answer,
                'relevant_data' => $relevantData,
                'context_used' => $context,
            ];

        } catch (Exception $e) {
            Log::error('Chatbot error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'query' => $query,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Similarity search untuk cari data paling relevan
     */
    private function similaritySearch(array $queryEmbedding, array $options = []): array
    {
        $searchType = $options['search_type'] ?? 'both'; // 'jaringan', 'spk', 'both'
        $topK = $options['top_k'] ?? $this->topK;
        $results = [];

        // Search di Jaringan Embeddings
        if (in_array($searchType, ['jaringan', 'both'])) {
            $jaringanResults = $this->searchJaringan($queryEmbedding, $topK);
            $results = array_merge($results, $jaringanResults);
        }

        // Search di SPK Embeddings
        if (in_array($searchType, ['spk', 'both'])) {
            $spkResults = $this->searchSpk($queryEmbedding, $topK);
            $results = array_merge($results, $spkResults);
        }

        // Sort by similarity dan ambil top K
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return array_slice($results, 0, $topK);
    }

    /**
     * Search di Jaringan Embeddings
     */
    private function searchJaringan(array $queryEmbedding, int $limit): array
    {
        $jaringanEmbeddings = JaringanEmbedding::all();
        $results = [];

        foreach ($jaringanEmbeddings as $item) {
            $embedding = $item->getEmbeddingArray();
            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            $results[] = [
                'type' => 'jaringan',
                'id' => $item->id_embedding,
                'no_jaringan' => $item->no_jaringan,
                'content_text' => $item->content_text,
                'similarity' => $similarity,
            ];
        }

        // Sort dan ambil top results
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return array_slice($results, 0, $limit);
    }

    /**
     * Search di SPK Embeddings
     */
    private function searchSpk(array $queryEmbedding, int $limit): array
    {
        $spkEmbeddings = SpkEmbedding::all();
        $results = [];

        foreach ($spkEmbeddings as $item) {
            $embedding = $item->getEmbeddingArray();
            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            $results[] = [
                'type' => 'spk',
                'id' => $item->id_embedding,
                'id_spk' => $item->id_spk,
                'no_spk' => $item->no_spk,
                'content_text' => $item->content_text,
                'similarity' => $similarity,
            ];
        }

        // Sort dan ambil top results
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return array_slice($results, 0, $limit);
    }

    /**
     * Calculate cosine similarity antara 2 vectors
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vecA); $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }

        $normA = sqrt($normA);
        $normB = sqrt($normB);

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / ($normA * $normB);
    }

    /**
     * Build context dari data yang relevan
     */
    private function buildContext(array $relevantData): string
    {
        if (empty($relevantData)) {
            return "Tidak ada data yang relevan ditemukan.";
        }

        $contextParts = ["=== DATA RELEVAN ===\n"];

        foreach ($relevantData as $index => $data) {
            $contextParts[] = "\n--- Data #" . ($index + 1) . " (Similarity: " . 
                              number_format($data['similarity'], 4) . ") ---";
            $contextParts[] = $data['content_text'];
        }

        return implode("\n", $contextParts);
    }

    /**
     * Call Flask API untuk chatbot
     */
    private function callFlaskChatbot(string $query, string $context): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->flaskApiUrl}/chat", [
                    'query' => $query,
                    'context' => $context,
                ]);

            if (!$response->successful()) {
                throw new Exception("Flask API error: " . $response->body());
            }

            $data = $response->json();

            if (!isset($data['answer'])) {
                throw new Exception("Invalid chatbot response from Flask API");
            }

            return $data['answer'];

        } catch (Exception $e) {
            Log::error('Failed to call Flask chatbot API', [
                'error' => $e->getMessage(),
                'url' => $this->flaskApiUrl
            ]);
            throw $e;
        }
    }
}