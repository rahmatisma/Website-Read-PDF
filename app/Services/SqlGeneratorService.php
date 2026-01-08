<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * 🔧 SQL Generator Service - SIMPLIFIED
 * 
 * Flask app.py sudah punya pattern-based SQL generation yang excellent!
 * Service ini hanya wrapper + validator
 */
class SqlGeneratorService
{
    protected string $flaskApiUrl;
    protected array $allowedTables;

    public function __construct()
    {
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://localhost:5000');
        
        // Whitelist allowed tables (security)
        $this->allowedTables = [
            'jaringan', 'spk', 'spk_pelaksanaan', 'spk_execution_info',
            'spk_informasi_gedung', 'spk_sarpen_ruang_server', 'spk_sarpen_tegangan',
            'spk_lokasi_antena', 'spk_perizinan_biaya_gedung', 'spk_penempatan_perangkat',
            'spk_perizinan_biaya_kawasan', 'spk_kawasan_umum', 'spk_data_splitter',
            'spk_hh_eksisting', 'spk_hh_baru', 'dokumentasi_foto', 'berita_acara',
            'list_item', 'form_checklist_wireline', 'fcw_waktu_pelaksanaan',
            'fcw_tegangan', 'fcw_checklist_item', 'fcw_data_perangkat',
            'fcw_guidance_foto', 'fcw_log', 'form_checklist_wireless',
            'fcwl_waktu_pelaksanaan', 'fcwl_tegangan', 'fcwl_indoor_area',
            'fcwl_indoor_parameter', 'fcwl_outdoor_area', 'fcwl_outdoor_parameter',
            'fcwl_perangkat_antenna', 'fcwl_cabling_installation',
            'fcwl_data_perangkat', 'fcwl_guidance_foto', 'fcwl_log'
        ];
    }

    /**
     * Generate SQL query (delegasi ke Flask pattern-based generator)
     * 
     * @param string $query User query
     * @param array $intent Intent classification result
     * @param array $context Current context
     * @return array SQL generation result
     */
    public function generate(string $query, array $intent, array $context = []): array
    {
        try {
            Log::info('🔧 SQL Generation Started', [
                'query' => $query,
                'intent_type' => $intent['type'],
                'context_received' => $context,
                'entities_in_intent' => $intent['entities'] ?? []
            ]);

            // ✅ REMOVE: Fallback logic tidak perlu lagi karena context sudah benar
            // Context sudah di-update di Controller dan ChatbotService
            
            // Call Flask /generate-sql
            $response = Http::timeout(60)->post("{$this->flaskApiUrl}/generate-sql", [
                'query' => $query,
                'intent' => $intent,
                'context' => $context,  // ✅ Context pasti benar
            ]);

            if (!$response->successful()) {
                throw new Exception("Flask API error: " . $response->body());
            }

            $result = $response->json();

            if (!isset($result['success']) || !$result['success']) {
                throw new Exception($result['error'] ?? 'SQL generation failed');
            }

            $sql = $result['sql'];
            
            // Validate SQL (TIDAK BERUBAH)
            $this->validateSQL($sql);

            Log::info('✅ SQL Generated', [
                'sql_preview' => substr($sql, 0, 100) . '...',
                'used_context' => $context  // ✅ TAMBAHAN: Log context yang dipakai
            ]);

            return [
                'success' => true,
                'sql' => $sql,
            ];

        } catch (Exception $e) {
            Log::error('❌ SQL Generation Failed', [
                'error' => $e->getMessage(),
                'context_at_failure' => $context  // ✅ TAMBAHAN: Log context saat error
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute generated SQL and return results
     */
    public function execute(string $sql): array
    {
        try {
            // Final validation before execution
            $this->validateSQL($sql);

            // Execute query
            $results = DB::select($sql);

            Log::info('✅ SQL Executed Successfully', [
                'row_count' => count($results)
            ]);

            return [
                'success' => true,
                'data' => $results,
                'count' => count($results),
            ];

        } catch (Exception $e) {
            Log::error('❌ SQL Execution Failed', [
                'sql' => substr($sql, 0, 200),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate SQL for security
     */
    private function validateSQL(string $sql): void
    {
        $sqlUpper = strtoupper(trim($sql));

        // Rule 1: MUST start with SELECT
        if (!str_starts_with($sqlUpper, 'SELECT')) {
            throw new Exception('Only SELECT queries are allowed');
        }

        // Rule 2: NO dangerous commands
        $forbiddenCommands = [
            'DROP ', 'DROP;', 
            'DELETE FROM', 'DELETE ',
            'UPDATE ', 'UPDATE;',
            'INSERT ', 'INSERT INTO',
            'ALTER ', 'ALTER TABLE',
            'TRUNCATE ', 
            'EXEC ', 'EXECUTE ',
            'LOAD_FILE', 'OUTFILE', 'DUMPFILE',
            'GRANT ', 'REVOKE ',
            'CREATE ', 'REPLACE '
        ];
        
        foreach ($forbiddenCommands as $command) {
            if (str_contains($sqlUpper, $command)) {
                throw new Exception("Forbidden SQL command detected: {$command}");
            }
        }
        
        // Rule 3: Must have FROM clause
        if (!str_contains($sqlUpper, 'FROM ')) {
            throw new Exception('SQL must contain FROM clause');
        }

        // Rule 4: Check table whitelist
        $foundValidTable = false;
        foreach ($this->allowedTables as $table) {
            $tableUpper = strtoupper($table);
            if (preg_match('/\b' . preg_quote($tableUpper, '/') . '\b/', $sqlUpper)) {
                $foundValidTable = true;
                break;
            }
        }
        
        if (!$foundValidTable) {
            throw new Exception('SQL must use at least one valid table from the schema');
        }
        
        Log::debug('✅ SQL validation passed');
    }
}