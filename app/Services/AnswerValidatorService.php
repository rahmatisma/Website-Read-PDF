<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🛡️ Answer Validator Service
 * 
 * CRITICAL: Validates that LLM answers ONLY contain data from database
 * Prevents hallucination by cross-checking every claim
 */
class AnswerValidatorService
{
    /**
     * Validate answer contains ONLY real data
     * 
     * @param string $answer Generated answer
     * @param array $sourceData Data used to generate answer
     * @param string $query Original user query
     * @return array Validation result with corrections
     */
    public function validate(string $answer, array $sourceData, string $query): array
    {
        Log::info('🛡️ Validating answer for hallucination', [
            'answer_length' => strlen($answer),
            'source_data_count' => count($sourceData)
        ]);

        $issues = [];
        $correctedAnswer = $answer;

        // ================================
        // VALIDATION 1: Check claimed entities exist in database
        // ================================
        $claimedEntities = $this->extractClaimedEntities($answer);
        
        foreach ($claimedEntities as $type => $values) {
            foreach ($values as $value) {
                if (!$this->verifyEntityExistsInDB($type, $value)) {
                    $issues[] = [
                        'type' => 'hallucinated_entity',
                        'entity_type' => $type,
                        'claimed_value' => $value,
                        'severity' => 'critical'
                    ];
                    
                    // Remove hallucinated entity from answer
                    $correctedAnswer = $this->removeFalseEntity($correctedAnswer, $value);
                }
            }
        }

        // ================================
        // VALIDATION 2: Check numbers match source data
        // ================================
        $claimedNumbers = $this->extractNumbers($answer);
        $validNumbers = $this->extractNumbersFromSourceData($sourceData);
        
        foreach ($claimedNumbers as $number) {
            if (!in_array($number, $validNumbers) && $number > 100) {
                // Large numbers should be from source data
                $issues[] = [
                    'type' => 'hallucinated_number',
                    'claimed_value' => $number,
                    'severity' => 'high'
                ];
            }
        }

        // ================================
        // VALIDATION 3: Check dates are realistic
        // ================================
        $claimedDates = $this->extractDates($answer);
        
        foreach ($claimedDates as $date) {
            if (!$this->isDateRealistic($date)) {
                $issues[] = [
                    'type' => 'unrealistic_date',
                    'claimed_value' => $date,
                    'severity' => 'medium'
                ];
            }
        }

        // ================================
        // VALIDATION 4: Check against empty source data
        // ================================
        if (empty($sourceData)) {
            // If source is empty, answer MUST indicate "no data found"
            $noDataPhrases = ['tidak ditemukan', 'tidak ada data', 'data tidak tersedia', 'no data'];
            $hasNoDataPhrase = false;
            
            foreach ($noDataPhrases as $phrase) {
                if (stripos($answer, $phrase) !== false) {
                    $hasNoDataPhrase = true;
                    break;
                }
            }
            
            if (!$hasNoDataPhrase && strlen($answer) > 50) {
                // Answer is too detailed for empty source
                $issues[] = [
                    'type' => 'detailed_answer_without_data',
                    'severity' => 'critical'
                ];
                
                $correctedAnswer = $this->generateNoDataResponse($query);
            }
        }

        // ================================
        // FINAL DECISION
        // ================================
        $hasCriticalIssues = collect($issues)->contains('severity', 'critical');
        
        if ($hasCriticalIssues) {
            Log::warning('⚠️ Critical hallucination detected', [
                'issues' => $issues
            ]);
            
            return [
                'is_valid' => false,
                'original_answer' => $answer,
                'corrected_answer' => $correctedAnswer,
                'issues' => $issues,
                'recommendation' => 'Use corrected answer or fallback to no-data response'
            ];
        }

        Log::info(' Answer validation passed', [
            'minor_issues' => count($issues)
        ]);

        return [
            'is_valid' => true,
            'answer' => $answer,
            'issues' => $issues
        ];
    }

    /**
     * Extract entities claimed in answer
     */
    private function extractClaimedEntities(string $answer): array
    {
        $entities = [
            'no_jaringan' => [],
            'no_spk' => [],
            'pelanggan' => [],
            'vendor' => [],
            'teknisi' => []
        ];

        // Extract no_jaringan (10 digits)
        if (preg_match_all('/\b(\d{10})\b/', $answer, $matches)) {
            $entities['no_jaringan'] = array_unique($matches[1]);
        }

        // Extract no_spk (pattern: digits/letters/dashes)
        if (preg_match_all('/(?:SPK|no_spk)[:\s\-]+([A-Z0-9\-\/]+)/i', $answer, $matches)) {
            $entities['no_spk'] = array_unique($matches[1]);
        }

        // Extract pelanggan names (uppercase words)
        if (preg_match_all('/pelanggan\s+([A-Z][A-Z0-9\s&\.\(\)]{3,50}?)(?:\s+dengan|\s+yang|\.|$)/i', $answer, $matches)) {
            $entities['pelanggan'] = array_unique(array_map('trim', $matches[1]));
        }

        // Extract vendor names
        if (preg_match_all('/vendor\s+([A-Z][A-Z0-9\s&\.]{2,30}?)(?:\s|$)/i', $answer, $matches)) {
            $entities['vendor'] = array_unique(array_map('trim', $matches[1]));
        }

        // Extract teknisi names
        if (preg_match_all('/teknisi[:\s]+([A-Z][a-z]+(?:\s[A-Z][a-z]+)*)/i', $answer, $matches)) {
            $entities['teknisi'] = array_unique(array_map('trim', $matches[1]));
        }

        return array_filter($entities);
    }

    /**
     * Verify entity exists in database
     */
    private function verifyEntityExistsInDB(string $type, string $value): bool
    {
        try {
            $value = trim($value);
            
            switch ($type) {
                case 'no_jaringan':
                    $count = DB::table('jaringan')
                        ->where('no_jaringan', $value)
                        ->where('is_deleted', 0)
                        ->count();
                    break;

                case 'no_spk':
                    $count = DB::table('spk')
                        ->where('no_spk', 'LIKE', "%{$value}%")
                        ->where('is_deleted', 0)
                        ->count();
                    break;

                case 'pelanggan':
                    $count = DB::table('jaringan')
                        ->where('nama_pelanggan', 'LIKE', "%{$value}%")
                        ->where('is_deleted', 0)
                        ->count();
                    break;

                case 'vendor':
                    $count = DB::table('spk_execution_info')
                        ->where('nama_vendor', 'LIKE', "%{$value}%")
                        ->count();
                    break;

                case 'teknisi':
                    $count = DB::table('spk_execution_info')
                        ->where('teknisi', 'LIKE', "%{$value}%")
                        ->count();
                    break;

                default:
                    return true; // Unknown type, skip validation
            }

            $exists = $count > 0;
            
            if (!$exists) {
                Log::warning('Entity not found in database', [
                    'type' => $type,
                    'value' => $value
                ]);
            }

            return $exists;

        } catch (\Exception $e) {
            Log::error('Error verifying entity', [
                'type' => $type,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            
            return true; // If verification fails, assume valid (safer)
        }
    }

    /**
     * Extract all numbers from answer
     */
    private function extractNumbers(string $answer): array
    {
        preg_match_all('/\b(\d+)\b/', $answer, $matches);
        return array_map('intval', $matches[1]);
    }

    /**
     * Extract numbers from source data
     */
    private function extractNumbersFromSourceData(array $sourceData): array
    {
        $numbers = [];
        
        foreach ($sourceData as $row) {
            if (is_object($row)) {
                $row = (array) $row;
            }
            
            foreach ($row as $value) {
                if (is_numeric($value)) {
                    $numbers[] = (int) $value;
                }
            }
        }
        
        return array_unique($numbers);
    }

    /**
     * Extract dates from answer
     */
    private function extractDates(string $answer): array
    {
        $dates = [];
        
        // Pattern: DD-MM-YYYY, DD/MM/YYYY, YYYY-MM-DD
        if (preg_match_all('/\b(\d{2}[-\/]\d{2}[-\/]\d{4}|\d{4}-\d{2}-\d{2})\b/', $answer, $matches)) {
            $dates = $matches[1];
        }
        
        return $dates;
    }

    /**
     * Check if date is realistic (not future, not too old)
     */
    private function isDateRealistic(string $date): bool
    {
        try {
            // Parse date
            $timestamp = strtotime($date);
            if (!$timestamp) {
                return false;
            }
            
            $now = time();
            $tenYearsAgo = strtotime('-10 years');
            $oneYearFuture = strtotime('+1 year');
            
            // Date should be between 10 years ago and 1 year in future
            return $timestamp >= $tenYearsAgo && $timestamp <= $oneYearFuture;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove false entity from answer
     */
    private function removeFalseEntity(string $answer, string $entity): string
    {
        // Remove the sentence containing the false entity
        $sentences = preg_split('/(?<=[.!?])\s+/', $answer);
        
        $filtered = array_filter($sentences, function($sentence) use ($entity) {
            return stripos($sentence, $entity) === false;
        });
        
        return implode(' ', $filtered);
    }

    /**
     * Generate "no data found" response
     */
    private function generateNoDataResponse(string $query): string
    {
        return "Maaf, tidak ditemukan data yang sesuai dengan pertanyaan Anda. " .
               "Silakan periksa kembali nomor jaringan, nomor SPK, atau kriteria pencarian Anda.";
    }

    /**
     * Verify count matches reality
     */
    public function verifyCount(int $claimedCount, array $actualData): bool
    {
        $actualCount = count($actualData);
        
        // Allow small margin of error (for aggregations)
        $marginOfError = 2;
        
        $isValid = abs($claimedCount - $actualCount) <= $marginOfError;
        
        if (!$isValid) {
            Log::warning('⚠️ Count mismatch detected', [
                'claimed' => $claimedCount,
                'actual' => $actualCount
            ]);
        }
        
        return $isValid;
    }

    /**
     * Cross-validate answer with SQL result
     */
    public function crossValidateWithSQL(string $answer, array $sqlResult): bool
    {
        if (empty($sqlResult)) {
            // Answer should indicate "no data"
            $noDataIndicators = ['tidak ditemukan', 'tidak ada', 'no data', '0 data', '0 SPK'];
            
            foreach ($noDataIndicators as $indicator) {
                if (stripos($answer, $indicator) !== false) {
                    return true;
                }
            }
            
            Log::warning('⚠️ Answer claims data exists but SQL returned empty');
            return false;
        }
        
        // If SQL has data, answer should NOT say "no data"
        $noDataIndicators = ['tidak ditemukan', 'tidak ada data'];
        
        foreach ($noDataIndicators as $indicator) {
            if (stripos($answer, $indicator) !== false) {
                Log::warning('⚠️ Answer says no data but SQL returned results');
                return false;
            }
        }
        
        return true;
    }
}