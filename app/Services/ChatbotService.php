<?php

namespace App\Services;

use App\Models\SpkEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ChatbotService
{
    protected EmbeddingService $embeddingService;
    protected string $flaskApiUrl;
    protected int $timeout;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->timeout = env('FLASK_API_TIMEOUT', 300);
    }

    /**
     * HYBRID CHATBOT: Database First + Dynamic RAG + Conversation Memory
     */
    public function chat(
        string $query, 
        array $conversationHistory = [],  // ← BARU
        array $currentContext = [],        // ← BARU
        array $options = []
    ): array
    {
        try {
            Log::info('Chatbot query received', [
                'query' => $query,
                'has_history' => !empty($conversationHistory),
                'has_context' => !empty($currentContext),
                'context' => $currentContext
            ]);

            // ================================
            // STEP 1: ENHANCE QUERY DENGAN CONTEXT
            // ================================
            $enhancedQuery = $this->enhanceQueryWithContext($query, $currentContext);
            
            Log::info('Query enhanced', [
                'original' => $query,
                'enhanced' => $enhancedQuery
            ]);

            // ================================
            // STEP 2: RULE-BASED DETECTION (Database Query)
            // ================================
            
            // Pattern 1: Nama pelanggan -> nomor jaringan
            if (preg_match('/pelanggan\s+([A-Z\s&\.]+?)\s+(?:memiliki|punya|ada)\s+(?:nomor\s+)?(?:jaringan|nojar)/i', $enhancedQuery, $matches)) {
                return $this->queryJaringanByCustomer(trim($matches[1]));
            }
            
            // ... (pattern lainnya tetap sama, gunakan $enhancedQuery)
            
            // ================================
            // STEP 3: RAG dengan Context-Aware
            // ================================
            
            $queryType = $this->detectQueryType($enhancedQuery);
            $ragParams = $this->getOptimalRagParams($queryType);
            
            // Generate embedding dari enhanced query
            $queryEmbedding = $this->embeddingService->generateEmbedding($enhancedQuery);
            
            // Similarity search dengan context awareness
            $relevantData = $this->contextAwareSimilaritySearch(
                $queryEmbedding, 
                $currentContext,  // ← Pass context
                $ragParams
            );
            
            // ... (rest of RAG code)
            
            $context = $this->buildContext($relevantData);
            
            // Call Flask dengan conversation history
            $answer = $this->callFlaskChatbot(
                $enhancedQuery, 
                $context,
                $conversationHistory  // ← Pass history
            );

            // ================================
            // STEP 4: EXTRACT ENTITIES dari jawaban
            // ================================
            $extractedEntities = $this->extractEntitiesFromAnswer($answer, $relevantData);

            Log::info('Chatbot response generated', [
                'query_type' => $queryType,
                'extracted_entities' => $extractedEntities
            ]);

            return [
                'success' => true,
                'query' => $query,
                'enhanced_query' => $enhancedQuery,
                'answer' => $answer,
                'source' => 'rag',
                'query_type' => $queryType,
                'rag_params' => $ragParams,
                'relevant_data' => $relevantData,
                'extracted_entities' => $extractedEntities,  // ← BARU untuk frontend
            ];

        } catch (Exception $e) {
            Log::error('Chatbot error', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'query' => $query,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Deteksi jenis query untuk dynamic parameters
     */
    private function detectQueryType(string $query): string
    {
        $query = strtolower($query);
        
        if (preg_match('/(?:semua|list|daftar|sebutkan|berapa banyak|ada berapa)/i', $query)) {
            return 'list';
        }
        
        if (preg_match('/(?:detail|lengkap|informasi lengkap|jelaskan tentang|apa isi)/i', $query)) {
            return 'detail';
        }
        
        if (preg_match('/(?:perbedaan|bandingkan|versus|vs|dibandingkan|beda)/i', $query)) {
            return 'comparison';
        }
        
        if (preg_match('/(?:analisis|bagaimana|mengapa|kenapa|apa penyebab|cara)/i', $query)) {
            return 'analysis';
        }
        
        return 'general';
    }

    /**
     * Dynamic RAG parameters berdasarkan query type
     */
    private function getOptimalRagParams(string $queryType): array
    {
        $params = [
            'list' => [
                'top_k' => 15,
                'min_similarity' => 0.4
            ],
            'detail' => [
                'top_k' => 3,
                'min_similarity' => 0.6
            ],
            'comparison' => [
                'top_k' => 8,
                'min_similarity' => 0.5
            ],
            'analysis' => [
                'top_k' => 5,
                'min_similarity' => 0.5
            ],
            'general' => [
                'top_k' => 5,
                'min_similarity' => 0.5
            ]
        ];
        
        return $params[$queryType] ?? $params['general'];
    }

    /**
     * DATABASE QUERY: Jaringan by customer name
     */
    private function queryJaringanByCustomer(string $customerName): array
    {
        $jaringan = DB::table('jaringan')
            ->where('nama_pelanggan', 'LIKE', "%{$customerName}%")
            ->select('no_jaringan', 'nama_pelanggan', 'lokasi_pelanggan', 'jasa', 'media_akses', 'kecepatan')
            ->first();
        
        if (!$jaringan) {
            $answer = "Tidak ditemukan pelanggan dengan nama **{$customerName}** dalam database.\n\nSilakan periksa kembali ejaan nama pelanggan.";
        } else {
            $answer = "**Data Pelanggan:**\n\n" .
                      "Nama: {$jaringan->nama_pelanggan}\n" .
                      "Nomor Jaringan: {$jaringan->no_jaringan}\n" .
                      "Lokasi: {$jaringan->lokasi_pelanggan}\n" .
                      "Jasa: {$jaringan->jasa}\n" .
                      "Media Akses: {$jaringan->media_akses}\n" .
                      "Kecepatan: {$jaringan->kecepatan}";
        }
        
        Log::info('Database query: Jaringan by customer', [
            'customer_name' => $customerName,
            'found' => $jaringan !== null
        ]);
        
        return [
            'success' => true,
            'query' => "pelanggan {$customerName}",
            'answer' => $answer,
            'source' => 'database',
            'data' => $jaringan,
        ];
    }

    /**
     * DATABASE QUERY: SPK by no_jaringan
     */
    private function querySpkByNoJaringan(string $noJaringan): array
    {
        $spks = DB::table('spk')
            ->where('no_jaringan', $noJaringan)
            ->select('no_spk', 'jenis_spk', 'tanggal_spk', 'document_type', 'no_mr', 'no_fps')
            ->orderBy('tanggal_spk', 'desc')
            ->get();
        
        if ($spks->isEmpty()) {
            $answer = "Tidak ditemukan SPK dengan nomor jaringan **{$noJaringan}**.";
        } else {
            $answer = "Ditemukan **{$spks->count()} SPK** untuk nomor jaringan **{$noJaringan}**:\n\n";
            
            // Group by jenis_spk
            $groupedByJenis = $spks->groupBy('jenis_spk');
            $answer .= "Ringkasan Jenis SPK:\n";
            foreach ($groupedByJenis as $jenis => $items) {
                $answer .= "   - {$jenis}: {$items->count()} SPK\n";
            }
            
            $answer .= "\nDetail SPK:\n";
            foreach ($spks as $index => $spk) {
                $answer .= "\n" . ($index + 1) . ". {$spk->no_spk}\n";
                $answer .= "   - Jenis: {$spk->jenis_spk}\n";
                $answer .= "   - Tanggal: {$spk->tanggal_spk}\n";
                if ($spk->document_type) {
                    $answer .= "   - Tipe: {$spk->document_type}\n";
                }
            }
        }
        
        Log::info('Database query: SPK by no_jaringan', [
            'no_jaringan' => $noJaringan,
            'spk_count' => $spks->count()
        ]);
        
        return [
            'success' => true,
            'query' => "nomor jaringan {$noJaringan}",
            'answer' => $answer,
            'source' => 'database',
            'data' => $spks,
        ];
    }

    /**
     * DATABASE QUERY: SPK detail by no_spk
     */
    private function querySpkDetail(string $noSpk): array
    {
        $spk = DB::table('spk')
            ->join('jaringan', 'spk.no_jaringan', '=', 'jaringan.no_jaringan')
            ->where('spk.no_spk', 'LIKE', "%{$noSpk}%")
            ->select('spk.*', 'jaringan.nama_pelanggan', 'jaringan.lokasi_pelanggan', 'jaringan.jasa')
            ->first();
        
        if (!$spk) {
            $answer = "Tidak ditemukan SPK dengan nomor **{$noSpk}**.";
        } else {
            $answer = "**Detail SPK {$spk->no_spk}**\n\n" .
                      "Informasi SPK:\n" .
                      "- Jenis: {$spk->jenis_spk}\n" .
                      "- Tanggal: {$spk->tanggal_spk}\n" .
                      "- Tipe Dokumen: {$spk->document_type}\n";
            
            if ($spk->no_mr) {
                $answer .= "- No MR: {$spk->no_mr}\n";
            }
            if ($spk->no_fps) {
                $answer .= "- No FPS: {$spk->no_fps}\n";
            }
            
            $answer .= "\nNomor Jaringan: {$spk->no_jaringan}\n\n" .
                       "Data Pelanggan:\n" .
                       "- Nama: {$spk->nama_pelanggan}\n" .
                       "- Lokasi: {$spk->lokasi_pelanggan}\n" .
                       "- Jasa: {$spk->jasa}";
        }
        
        Log::info('Database query: SPK detail', [
            'no_spk' => $noSpk,
            'found' => $spk !== null
        ]);
        
        return [
            'success' => true,
            'query' => "no spk {$noSpk}",
            'answer' => $answer,
            'source' => 'database',
            'data' => $spk,
        ];
    }

    /**
     * DATABASE QUERY: SPK by jenis
     */
    private function querySpkByJenis(string $query): array
    {
        $jenisMap = [
            'aktivasi' => 'aktivasi',
            'instalasi' => 'instalasi',
            'survey' => 'survey',
            'maintenance' => 'maintenance',
            'dismantle' => 'dismantle',
        ];
        
        $jenis = null;
        foreach ($jenisMap as $keyword => $value) {
            if (stripos($query, $keyword) !== false) {
                $jenis = $value;
                break;
            }
        }
        
        if (!$jenis) {
            return [
                'success' => false,
                'query' => $query,
                'error' => 'Tidak dapat mendeteksi jenis SPK dari pertanyaan',
            ];
        }
        
        $spks = DB::table('spk')
            ->where('jenis_spk', $jenis)
            ->select('no_spk', 'jenis_spk', 'tanggal_spk', 'no_jaringan')
            ->orderBy('tanggal_spk', 'desc')
            ->limit(20)
            ->get();
        
        if ($spks->isEmpty()) {
            $answer = "Tidak ditemukan SPK dengan jenis **{$jenis}**.";
        } else {
            $answer = "Ditemukan **{$spks->count()} SPK** dengan jenis **{$jenis}**:\n\n";
            
            foreach ($spks as $index => $spk) {
                $answer .= ($index + 1) . ". {$spk->no_spk} | {$spk->tanggal_spk}\n";
            }
            
            if ($spks->count() >= 20) {
                $answer .= "\n*Menampilkan 20 SPK terbaru. Total mungkin lebih banyak.*";
            }
        }
        
        Log::info('Database query: SPK by jenis', [
            'jenis' => $jenis,
            'spk_count' => $spks->count()
        ]);
        
        return [
            'success' => true,
            'query' => "SPK jenis {$jenis}",
            'answer' => $answer,
            'source' => 'database',
            'data' => $spks,
        ];
    }

    /**
     * Similarity search dengan context awareness
     */
    private function contextAwareSimilaritySearch(
        array $queryEmbedding, 
        array $context,
        array $options = []
    ): array {
        $topK = $options['top_k'] ?? 5;
        $minSimilarity = $options['min_similarity'] ?? 0.5;
        
        $query = SpkEmbedding::query();
        
        // PRIORITASKAN embedding yang match dengan context
        if (!empty($context['last_nojar'])) {
            // Cari yang mengandung nojar yang sedang dibahas
            $nojarMatches = SpkEmbedding::where('content_text', 'LIKE', "%{$context['last_nojar']}%")->get();
            
            if ($nojarMatches->isNotEmpty()) {
                Log::info('Context match found', [
                    'nojar' => $context['last_nojar'],
                    'matches' => $nojarMatches->count()
                ]);
                
                // Gunakan ini sebagai kandidat utama
                $spkEmbeddings = $nojarMatches;
            } else {
                $spkEmbeddings = SpkEmbedding::all();
            }
        } else {
            $spkEmbeddings = SpkEmbedding::all();
        }
        
        if ($spkEmbeddings->isEmpty()) {
            Log::warning('No SPK embeddings found in database');
            return [];
        }
        
        $results = [];

        foreach ($spkEmbeddings as $item) {
            $embedding = $item->getEmbeddingArray();
            
            if (empty($embedding)) {
                continue;
            }
            
            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);
            
            // Boost similarity jika match dengan context
            if (!empty($context['last_nojar']) && 
                strpos($item->content_text, $context['last_nojar']) !== false) {
                $similarity *= 1.2; // Boost 20%
                Log::info('Similarity boosted for context match', [
                    'no_spk' => $item->no_spk,
                    'original_similarity' => $similarity / 1.2,
                    'boosted_similarity' => $similarity
                ]);
            }
            
            if ($similarity >= $minSimilarity) {
                $results[] = [
                    'type' => 'spk',
                    'id' => $item->id_embedding,
                    'id_spk' => $item->id_spk,
                    'no_spk' => $item->no_spk,
                    'content_text' => $item->content_text,
                    'similarity' => min($similarity, 1.0), // Cap at 1.0
                ];
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        
        return array_slice($results, 0, $topK);
    }

    /**
     * Calculate cosine similarity
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            Log::warning('Vector dimension mismatch', [
                'vecA_length' => count($vecA),
                'vecB_length' => count($vecB)
            ]);
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
     * Build context dari relevant data
     */
    private function buildContext(array $relevantData): string
    {
        if (empty($relevantData)) {
            return "Tidak ada data yang relevan ditemukan dalam database.";
        }

        $contextParts = ["=== DATA SPK YANG RELEVAN ===\n"];

        foreach ($relevantData as $index => $data) {
            $contextParts[] = "\n--- SPK #" . ($index + 1) . 
                            " | No SPK: " . ($data['no_spk'] ?? 'N/A') .
                            " | Similarity: " . number_format($data['similarity'], 4) . " ---";
            $contextParts[] = $data['content_text'];
            $contextParts[] = "";
        }

        return implode("\n", $contextParts);
    }

    /**
     * Call Flask API untuk chatbot dengan conversation history
     */
    private function callFlaskChatbot(
        string $query, 
        string $context,
        array $conversationHistory = []
    ): string {
        try {
            $payload = [
                'query' => $query,
                'context' => $context,
            ];
            
            // Tambahkan conversation history jika ada
            if (!empty($conversationHistory)) {
                // Ambil 5 percakapan terakhir saja
                $payload['conversation_history'] = array_slice($conversationHistory, -5);
            }
            
            Log::info('Calling Flask chatbot', [
                'query' => $query,
                'has_history' => !empty($conversationHistory),
                'history_count' => count($conversationHistory)
            ]);
            
            $response = Http::timeout($this->timeout)
                ->post("{$this->flaskApiUrl}/chat", $payload);

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
    /**
     * Enhance query dengan context dari percakapan sebelumnya
     */
    private function enhanceQueryWithContext(string $query, array $context): string
    {
        // Jika tidak ada context, return query original
        if (empty($context)) {
            return $query;
        }
        
        $queryLower = strtolower($query);
        
        // Deteksi pronoun reference (nya, itu, tersebut, dia)
        $hasPronoun = preg_match('/\b(nya|itu|tersebut|dia|ini)\b/i', $query);
        
        if (!$hasPronoun) {
            return $query; // Tidak perlu enhancement
        }
        
        // Build enhancement based on available context
        $enhancements = [];
        
        // Jika ada nojar di context
        if (!empty($context['last_nojar'])) {
            $enhancements[] = "nojar {$context['last_nojar']}";
        }
        
        // Jika ada pelanggan di context
        if (!empty($context['last_pelanggan'])) {
            $enhancements[] = "pelanggan {$context['last_pelanggan']}";
        }
        
        // Jika ada SPK di context
        if (!empty($context['last_spk'])) {
            $enhancements[] = "SPK {$context['last_spk']}";
        }
        
        if (empty($enhancements)) {
            return $query;
        }
        
        // Contoh:
        // "POP nya apa?" → "POP nya apa? (merujuk ke nojar 2023390898)"
        $enhanced = $query . ' (merujuk ke ' . implode(', ', $enhancements) . ')';
        
        return $enhanced;
    }

    /**
     * Extract entities dari jawaban untuk tracking context
     */
    private function extractEntitiesFromAnswer(string $answer, array $relevantData): array
    {
        $entities = [];
        
        // Extract dari relevant data (lebih akurat)
        if (!empty($relevantData)) {
            $firstData = $relevantData[0];
            
            // Coba extract dari content_text
            if (isset($firstData['content_text'])) {
                $content = $firstData['content_text'];
                
                // Extract nomor jaringan
                if (preg_match('/Nomor Jaringan:\s*(\d+)/i', $content, $matches)) {
                    $entities['nojar'] = $matches[1];
                }
                
                // Extract nama pelanggan
                if (preg_match('/Nama Pelanggan:\s*(.+?)(?:\n|$)/i', $content, $matches)) {
                    $entities['pelanggan'] = trim($matches[1]);
                }
                
                // Extract nomor SPK
                if (preg_match('/Nomor SPK:\s*(.+?)(?:\n|$)/i', $content, $matches)) {
                    $entities['spk'] = trim($matches[1]);
                }
                
                // Extract POP
                if (preg_match('/POP:\s*(.+?)(?:\n|$)/i', $content, $matches)) {
                    $entities['pop'] = trim($matches[1]);
                }
            }
        }
        
        // Fallback: Extract dari answer text
        if (empty($entities['nojar'])) {
            if (preg_match('/\b(\d{10})\b/', $answer, $matches)) {
                $entities['nojar'] = $matches[1];
            }
        }
        
        return $entities;
    }
}