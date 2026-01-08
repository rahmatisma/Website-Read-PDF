<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ChatbotService
{
    protected EmbeddingService $embeddingService;
    protected IntentClassifierService $intentClassifier;
    protected SqlGeneratorService $sqlGenerator;
    protected string $flaskApiUrl;
    protected int $timeout;

    public function __construct(
        EmbeddingService $embeddingService,
        IntentClassifierService $intentClassifier,
        SqlGeneratorService $sqlGenerator
    ) {
        $this->embeddingService = $embeddingService;
        $this->intentClassifier = $intentClassifier;
        $this->sqlGenerator = $sqlGenerator;
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->timeout = env('FLASK_API_TIMEOUT', 300);
    }

    /**
     * 🚀 HYBRID CHATBOT with 3-Layer Intelligence
     * 
     * Layer 1: Intent Classification (LLM decides strategy)
     * Layer 2: Smart Execution (SQL Generator OR RAG Search)
     * Layer 3: Answer Synthesis (Verified response formatting)
     */
    public function chat(
        string $query,
        array $conversationHistory = [],
        array $currentContext = [],
        array $options = []
    ): array {
        try {
            Log::info('🤖 Chatbot query received', [
                'query' => $query,
                'has_history' => !empty($conversationHistory),
                'incoming_context' => $currentContext,  // ✅ Context sudah benar dari controller
            ]);

            // STEP 0: Out-of-scope detection
            if (!$this->isDocumentRelatedQuery($query)) {
                return [
                    'success' => true,
                    'query' => $query,
                    'answer' => $this->getOutOfScopeMessage(),
                    'source' => 'out_of_scope',
                ];
            }

            // STEP 1: INTENT CLASSIFICATION
            $intent = $this->intentClassifier->classify($query, $currentContext);

            Log::info('🎯 Intent classified', [
                'type' => $intent['type'],
                'strategy' => $intent['strategy'],
                'confidence' => $intent['confidence'],
                'entities_from_intent' => $intent['entities'] ?? []
            ]);

            // ✅✅✅ STEP 2: MERGE CONTEXT (SIMPLIFIED) ✅✅✅
            // Merge entities dari intent ke current context
            if (!empty($intent['entities'])) {
                $currentContext = array_merge($currentContext, array_filter($intent['entities']));
                
                Log::info('✅ Context merged with intent entities', [
                    'final_context' => $currentContext
                ]);
            }
            // ✅✅✅ END OF SIMPLIFICATION ✅✅✅

            // STEP 3: SMART EXECUTION
            $executionResult = $this->executeByStrategy(
                $query,
                $intent,
                $currentContext,  // ✅ Context sudah final dan benar
                $conversationHistory
            );

            if (!$executionResult['success']) {
                Log::warning('Primary strategy failed, trying fallback');
                $executionResult = $this->executeFallback($query, $currentContext, $conversationHistory);
            }

            // STEP 4: ANSWER SYNTHESIS (TIDAK BERUBAH)
            $finalAnswer = $this->synthesizeAnswer(
                $query,
                $executionResult,
                $intent,
                $currentContext
            );

            // Extract entities (TIDAK BERUBAH)
            $extractedEntities = $this->extractEntitiesFromAnswer(
                $finalAnswer['answer'],
                $executionResult['data'] ?? []
            );

            // ✅ TAMBAHAN: Merge untuk final context
            $finalContext = array_merge($currentContext, array_filter($extractedEntities));

            Log::info('✅ Chatbot response generated', [
                'source' => $finalAnswer['source'],
                'strategy' => $intent['strategy'],
                'final_context' => $finalContext  // ✅ TAMBAHAN: Log final
            ]);

            return [
                'success' => true,
                'query' => $query,
                'answer' => $finalAnswer['answer'],
                'source' => $finalAnswer['source'],
                'strategy' => $intent['strategy'],
                'query_type' => $intent['type'],
                'extracted_entities' => $finalContext,  // ✅ UBAH: Return merged context
                'data' => $executionResult['data'] ?? null,
            ];

        } catch (Exception $e) {
            Log::error('❌ Chatbot error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'query' => $query,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute query based on strategy
     */
    private function executeByStrategy(
        string $query,
        array $intent,
        array $context,
        array $history
    ): array {
        switch ($intent['strategy']) {
            case 'SQL':
                return $this->executeSQLStrategy($query, $intent, $context);
                
            case 'RAG':
                return $this->executeRAGStrategy($query, $context, $history);
                
            case 'HYBRID':
                return $this->executeHybridStrategy($query, $intent, $context, $history);
                
            default:
                // Default to RAG if strategy unknown
                return $this->executeRAGStrategy($query, $context, $history);
        }
    }

    /**
     * SQL Strategy: Generate and execute SQL
     */
    private function executeSQLStrategy(string $query, array $intent, array $context): array
    {
        try {
            Log::info('🔧 Executing SQL Strategy');

            // Generate SQL
            $sqlResult = $this->sqlGenerator->generate($query, $intent, $context);
            
            if (!$sqlResult['success']) {
                throw new Exception($sqlResult['error'] ?? 'SQL generation failed');
            }

            // Execute SQL
            $execResult = $this->sqlGenerator->execute($sqlResult['sql']);
            
            if (!$execResult['success']) {
                throw new Exception($execResult['error'] ?? 'SQL execution failed');
            }

            // Verify data exists
            if (empty($execResult['data'])) {
                return [
                    'success' => true,
                    'data' => [],
                    'sql' => $sqlResult['sql'],
                    'message' => 'No data found',
                ];
            }

            Log::info('✅ SQL Strategy succeeded', [
                'sql' => $sqlResult['sql'],
                'row_count' => $execResult['count']
            ]);

            return [
                'success' => true,
                'data' => $execResult['data'],
                'sql' => $sqlResult['sql'],
                'count' => $execResult['count'],
            ];

        } catch (Exception $e) {
            Log::error('❌ SQL Strategy failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * RAG Strategy: Semantic search + LLM
     */
    private function executeRAGStrategy(string $query, array $context, array $history): array
    {
        try {
            Log::info('🔍 Executing RAG Strategy');

            // Enhance query with context
            $enhancedQuery = $this->enhanceQueryWithContext($query, $context);

            // Generate embedding
            $queryEmbedding = $this->embeddingService->generateEmbedding($enhancedQuery);

            // Similarity search
            $relevantData = $this->contextAwareSimilaritySearch(
                $queryEmbedding,
                $context,
                ['top_k' => 5, 'min_similarity' => 0.4]
            );

            // Build context string
            $contextString = $this->buildContext($relevantData);

            // Call LLM
            $answer = $this->callFlaskChatbot($enhancedQuery, $contextString, $history);

            Log::info('✅ RAG Strategy succeeded', [
                'relevant_data_count' => count($relevantData)
            ]);

            return [
                'success' => true,
                'data' => $relevantData,
                'answer' => $answer,
                'enhanced_query' => $enhancedQuery,
            ];

        } catch (Exception $e) {
            Log::error('❌ RAG Strategy failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hybrid Strategy: Combine SQL + RAG
     */
    private function executeHybridStrategy(
        string $query,
        array $intent,
        array $context,
        array $history
    ): array {
        try {
            Log::info('🔀 Executing Hybrid Strategy');

            // Execute both strategies
            $sqlResult = $this->executeSQLStrategy($query, $intent, $context);
            $ragResult = $this->executeRAGStrategy($query, $context, $history);

            // Combine results
            $combinedData = [
                'sql_data' => $sqlResult['data'] ?? [],
                'rag_data' => $ragResult['data'] ?? [],
            ];

            Log::info('✅ Hybrid Strategy succeeded');

            return [
                'success' => true,
                'data' => $combinedData,
                'sql_result' => $sqlResult,
                'rag_result' => $ragResult,
            ];

        } catch (Exception $e) {
            Log::error('❌ Hybrid Strategy failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fallback execution when primary fails
     */
    private function executeFallback(string $query, array $context, array $history): array
    {
        Log::info('🔄 Executing fallback to RAG');
        
        // Always fallback to RAG (safer)
        return $this->executeRAGStrategy($query, $context, $history);
    }

    /**
     * Synthesize final answer from execution results
     */
    private function synthesizeAnswer(
        string $query,
        array $executionResult,
        array $intent,
        array $context
    ): array {
        // If execution failed
        if (!$executionResult['success']) {
            return [
                'answer' => "Maaf, saya tidak dapat memproses pertanyaan Anda saat ini. Silakan coba dengan pertanyaan yang lebih spesifik.",
                'source' => 'error',
            ];
        }

        // If SQL strategy and has data
        if (!empty($executionResult['sql']) && !empty($executionResult['data'])) {
            $answer = $this->formatSQLResult($executionResult['data'], $query, $context);
            return [
                'answer' => $answer,
                'source' => 'direct_sql',
            ];
        }

        // If SQL strategy but no data
        if (!empty($executionResult['sql']) && empty($executionResult['data'])) {
            return [
                'answer' => $this->getNoDataMessage($query, $context),
                'source' => 'direct_sql_empty',
            ];
        }

        // If RAG strategy
        if (!empty($executionResult['answer'])) {
            return [
                'answer' => $executionResult['answer'],
                'source' => 'rag',
            ];
        }

        // If hybrid strategy
        if (!empty($executionResult['sql_result']) && !empty($executionResult['rag_result'])) {
            // Combine SQL data with RAG context
            $sqlAnswer = $this->formatSQLResult(
                $executionResult['sql_result']['data'] ?? [],
                $query,
                $context
            );
            
            $ragContext = $executionResult['rag_result']['answer'] ?? '';
            
            $combinedAnswer = $sqlAnswer . "\n\n" . $ragContext;
            
            return [
                'answer' => $combinedAnswer,
                'source' => 'hybrid',
            ];
        }

        // Default
        return [
            'answer' => "Tidak ada data yang ditemukan untuk pertanyaan Anda.",
            'source' => 'empty',
        ];
    }

    /**
     * Format SQL result into natural language
     */
    private function formatSQLResult(array $data, string $query, array $context): string
    {
        if (empty($data)) {
            return $this->getNoDataMessage($query, $context);
        }

        // Detect query type from data structure
        $firstRow = $data[0];
        
        // Count query
        if (isset($firstRow->jumlah_spk) || isset($firstRow->count) || isset($firstRow->total)) {
            $count = $firstRow->jumlah_spk ?? $firstRow->count ?? $firstRow->total ?? 0;
            
            if (preg_match('/spk/i', $query)) {
                $contextInfo = !empty($context['last_nojar']) 
                    ? " untuk nomor jaringan **{$context['last_nojar']}**"
                    : "";
                    
                return "Ditemukan **{$count} SPK**{$contextInfo}.";
            }
            
            return "Ditemukan **{$count}** hasil.";
        }

        // List query
        if (count($data) > 1) {
            return $this->formatListResult($data, $query, $context);
        }

        // Single detail query
        return $this->formatDetailResult($firstRow, $query, $context);
    }

    /**
     * Format list result
     */
    private function formatListResult(array $data, string $query, array $context): string
    {
        $contextInfo = !empty($context['last_nojar']) 
            ? " untuk nomor jaringan **{$context['last_nojar']}**"
            : "";

        $answer = "Ditemukan **" . count($data) . " data**{$contextInfo}:\n\n";

        foreach ($data as $index => $row) {
            $answer .= ($index + 1) . ". ";
            
            // Format based on available fields
            $rowArray = (array) $row;
            
            // SPK format
            if (isset($row->no_spk)) {
                $answer .= "**{$row->no_spk}**";
                if (isset($row->jenis_spk)) {
                    $answer .= " - {$row->jenis_spk}";
                }
                if (isset($row->tanggal_spk)) {
                    $answer .= " ({$row->tanggal_spk})";
                }
            }
            // Teknisi format
            elseif (isset($row->teknisi)) {
                $answer .= "**{$row->teknisi}**";
                if (isset($row->nama_vendor)) {
                    $answer .= " dari {$row->nama_vendor}";
                }
            }
            // Generic format
            else {
                $firstField = array_values($rowArray)[0];
                $answer .= "**{$firstField}**";
            }
            
            $answer .= "\n";
        }

        return $answer;
    }

    /**
     * Format detail result
     */
    private function formatDetailResult(object $row, string $query, array $context): string
    {
        $rowArray = (array) $row;
        $answer = "**Detail Data:**\n\n";

        foreach ($rowArray as $key => $value) {
            if ($value === null) continue;
            
            // Format key (snake_case to Title Case)
            $formattedKey = ucwords(str_replace('_', ' ', $key));
            
            $answer .= "**{$formattedKey}**: {$value}\n";
        }

        return $answer;
    }

    /**
     * Get "no data" message
     */
    private function getNoDataMessage(string $query, array $context): string
    {
        $contextInfo = "";
        
        if (!empty($context['last_nojar'])) {
            $contextInfo = " untuk nomor jaringan **{$context['last_nojar']}**";
        } elseif (!empty($context['last_spk'])) {
            $contextInfo = " untuk SPK **{$context['last_spk']}**";
        }

        return "Tidak ditemukan data{$contextInfo} yang sesuai dengan pertanyaan Anda.";
    }

    /**
     * Get out-of-scope message
     */
    private function getOutOfScopeMessage(): string
    {
        return "Maaf, saya hanya dapat membantu menjawab pertanyaan terkait:\n\n" .
            "✅ Dokumen SPK (Surat Perintah Kerja)\n" .
            "✅ Form Checklist (Wireline & Wireless)\n" .
            "✅ Data Pelanggan dan Jaringan\n" .
            "✅ Informasi Teknisi dan Vendor\n" .
            "✅ Detail Pelaksanaan Pekerjaan\n\n" .
            "Silakan ajukan pertanyaan seputar dokumen-dokumen tersebut. 😊";
    }

    /**
     * Check if query is document-related
     */
    private function isDocumentRelatedQuery(string $query): bool
    {
        $queryLower = strtolower($query);

        $documentKeywords = [
            'spk', 'jaringan', 'nojar', 'pelanggan', 'teknisi', 'vendor',
            'pop', 'lokasi', 'instalasi', 'survey', 'checklist', 'wireline',
            'wireless', 'kecepatan', 'foto', 'dokumentasi', 'tanggal',
            'berapa', 'siapa', 'dimana', 'kapan', 'ada'
        ];

        foreach ($documentKeywords as $keyword) {
            if (str_contains($queryLower, $keyword)) {
                return true;
            }
        }

        return str_word_count($query) >= 3 || preg_match('/\d{10}/', $query);
    }

    // ============================================
    // HELPER METHODS (dari service lama)
    // ============================================

    private function enhanceQueryWithContext(string $query, array $context): string
    {
        if (empty($context)) {
            return $query;
        }

        $queryLower = strtolower($query);
        $isShortQuery = str_word_count($query) <= 5;
        $hasPronoun = preg_match('/\b(nya|itu|tersebut|dia|ini)\b/i', $query);

        if (!$hasPronoun && !$isShortQuery) {
            return $query;
        }

        $enhancements = [];
        if (!empty($context['last_nojar'])) {
            $enhancements[] = "nojar {$context['last_nojar']}";
        }
        if (!empty($context['last_pelanggan'])) {
            $enhancements[] = "pelanggan {$context['last_pelanggan']}";
        }
        if (!empty($context['last_spk'])) {
            $enhancements[] = "SPK {$context['last_spk']}";
        }

        if (empty($enhancements)) {
            return $query;
        }

        return $query . ' (merujuk ke ' . implode(', ', $enhancements) . ')';
    }

    public function contextAwareSimilaritySearch(
        array $queryEmbedding,
        array $context,
        array $options = []
    ): array {
        $topK = $options['top_k'] ?? 5;
        $minSimilarity = $options['min_similarity'] ?? 0.4;

        $allResults = [];

        // Search jaringan embeddings
        $jaringanEmbeddings = \App\Models\JaringanEmbedding::all();
        foreach ($jaringanEmbeddings as $item) {
            $embedding = $item->getEmbeddingArray();
            if (empty($embedding)) continue;

            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            if (!empty($context['last_nojar']) && 
                strpos($item->content_text, $context['last_nojar']) !== false) {
                $similarity *= 1.2;
            }

            if ($similarity >= $minSimilarity) {
                $allResults[] = [
                    'type' => 'jaringan',
                    'content_text' => $item->content_text,
                    'similarity' => min($similarity, 1.0),
                ];
            }
        }

        // Search SPK embeddings
        $spkEmbeddings = \App\Models\SpkEmbedding::all();
        foreach ($spkEmbeddings as $item) {
            $embedding = $item->getEmbeddingArray();
            if (empty($embedding)) continue;

            $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);

            if (!empty($context['last_spk']) && 
                strpos($item->content_text, $context['last_spk']) !== false) {
                $similarity *= 1.2;
            }

            if ($similarity >= $minSimilarity) {
                $allResults[] = [
                    'type' => 'spk',
                    'content_text' => $item->content_text,
                    'similarity' => min($similarity, 1.0),
                ];
            }
        }

        usort($allResults, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        return array_slice($allResults, 0, $topK);
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) return 0.0;

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

        return ($normA == 0 || $normB == 0) ? 0.0 : $dotProduct / ($normA * $normB);
    }

    private function buildContext(array $relevantData): string
    {
        if (empty($relevantData)) {
            return "Tidak ada data relevan.";
        }

        $contextParts = ["=== DATA YANG RELEVAN ===\n"];
        foreach ($relevantData as $index => $data) {
            $type = $data['type'] ?? 'unknown';
            $contextParts[] = "\n--- " . strtoupper($type) . " #" . ($index + 1) . " ---";
            $contextParts[] = "Similarity: " . number_format($data['similarity'], 4);
            $contextParts[] = "\n" . $data['content_text'];
        }

        return implode("\n", $contextParts);
    }

    private function callFlaskChatbot(string $query, string $context, array $history = []): string
    {
        $payload = ['query' => $query, 'context' => $context];
        if (!empty($history)) {
            $payload['conversation_history'] = array_slice($history, -5);
        }

        $response = Http::timeout($this->timeout)
            ->post("{$this->flaskApiUrl}/chat", $payload);

        if (!$response->successful()) {
            throw new Exception("Flask API error: " . $response->body());
        }

        $data = $response->json();
        return $data['answer'] ?? 'Tidak ada jawaban.';
    }

    /**
     * Extract entities from answer (FIXED REGEX)
     */
    private function extractEntitiesFromAnswer(string $answer, array $data): array
    {
        $entities = [];

        // Priority 1: From SQL data (most accurate)
        if (!empty($data)) {
            $firstRow = is_object($data[0]) ? (array) $data[0] : $data[0];
            
            if (isset($firstRow['no_jaringan'])) {
                $entities['last_nojar'] = $firstRow['no_jaringan'];
            }
            if (isset($firstRow['no_spk'])) {
                $entities['last_spk'] = $firstRow['no_spk'];
            }
            if (isset($firstRow['nama_pelanggan'])) {
                $entities['last_pelanggan'] = $firstRow['nama_pelanggan'];
            }
        }

        // Priority 2: Extract from answer text (fallback)
        
        // ✅ Extract nojar (exactly 10 digits)
        if (empty($entities['last_nojar'])) {
            if (preg_match('/\b(\d{10})\b/', $answer, $match)) {
                $entities['last_nojar'] = $match[1];
                Log::info('📝 Extracted nojar from answer', ['nojar' => $match[1]]);
            }
        }
        
        // ✅ Extract pelanggan (improved regex)
        if (empty($entities['last_pelanggan'])) {
            // Pattern: "pelanggan [NAMA BESAR]" atau "untuk pelanggan [NAMA]"
            if (preg_match('/(?:untuk\s+)?pelanggan\s+([A-Z][A-Z0-9\s&\.\(\)]+?)(?:\s+dengan|\s+yang|\s+di|\s+adalah|\.|\n|$)/i', $answer, $match)) {
                $pelanggan = trim($match[1]);
                // Remove trailing words yang tidak perlu
                $pelanggan = preg_replace('/\s+(dengan|yang|di|adalah)$/', '', $pelanggan);
                $entities['last_pelanggan'] = $pelanggan;
                Log::info('📝 Extracted pelanggan from answer', ['pelanggan' => $pelanggan]);
            }
        }

        // ✅ Extract SPK number (improved pattern + validation)
        if (empty($entities['last_spk'])) {
            // Pattern: "SPK 298785" atau "no_spk: ABC123" atau "SPK-2024-001"
            if (preg_match('/(?:SPK|no_spk)[:\s\-]+([A-Z0-9\-\/]+)/i', $answer, $match)) {
                $spk = trim($match[1]);
                
                // ✅ CRITICAL: Validate SPK is NOT a nojar (10 digits)
                $is_10_digit_nojar = preg_match('/^\d{10}$/', $spk);
                
                // SPK harus minimal 3 karakter, bukan "nojar", dan bukan 10 digit number
                if (strlen($spk) >= 3 && 
                    strtolower($spk) !== 'nojar' && 
                    !$is_10_digit_nojar) {
                    
                    $entities['last_spk'] = $spk;
                    Log::info('📝 Extracted SPK from answer', ['spk' => $spk]);
                } else if ($is_10_digit_nojar) {
                    Log::warning('⚠️  Skipped extracting nojar as SPK', ['value' => $spk]);
                }
            }
        }
        
        // ✅ Validate extracted entities
        if (isset($entities['last_pelanggan'])) {
            $pelanggan = $entities['last_pelanggan'];
            
            // Remove invalid endings
            $invalidEndings = ['dengan', 'yang', 'adalah', 'di', 'untuk'];
            foreach ($invalidEndings as $ending) {
                if (str_ends_with(strtolower($pelanggan), $ending)) {
                    $pelanggan = trim(substr($pelanggan, 0, -strlen($ending)));
                }
            }
            
            // Minimum length validation
            if (strlen($pelanggan) < 3) {
                unset($entities['last_pelanggan']);
            } else {
                $entities['last_pelanggan'] = $pelanggan;
            }
        }
        
        // ✅ Log final extracted entities
        if (!empty($entities)) {
            Log::info('✅ Final extracted entities', $entities);
        }

        return $entities;
    }
}