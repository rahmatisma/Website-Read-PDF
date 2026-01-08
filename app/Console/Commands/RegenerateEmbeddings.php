<?php

namespace App\Console\Commands;

use App\Services\EmbeddingService;
use App\Models\Jaringan;
use App\Models\Spk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class RegenerateEmbeddings extends Command
{
    protected $signature = 'embeddings:regenerate 
                            {--type=both : Type to regenerate (jaringan, spk, or both)}
                            {--limit= : Limit number of records per type}
                            {--force : Force regenerate existing embeddings}';

    protected $description = 'Regenerate embeddings for Jaringan and SPK data';

    protected EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        parent::__construct();
        $this->embeddingService = $embeddingService;
    }

    public function handle()
    {
        $type = strtolower($this->option('type'));
        $limit = $this->option('limit');
        $force = $this->option('force');

        // Validasi type
        if (!in_array($type, ['jaringan', 'spk', 'both'])) {
            $this->error("❌ Invalid type: {$type}");
            $this->info("Valid types: jaringan, spk, both");
            return Command::FAILURE;
        }

        $this->info("🚀 Starting embeddings regeneration...");
        $this->info("Type: {$type}");
        if ($limit) {
            $this->info("Limit: {$limit} per type");
        }
        if ($force) {
            $this->warn("⚠️  Force mode: Will regenerate existing embeddings");
        }
        $this->newLine();

        $results = [
            'jaringan' => ['success' => 0, 'failed' => 0],
            'spk' => ['success' => 0, 'failed' => 0],
        ];

        // ================================
        // PROCESS JARINGAN EMBEDDINGS
        // ================================
        if ($type === 'jaringan' || $type === 'both') {
            $this->info("📊 Processing JARINGAN embeddings...");
            $jaringanResults = $this->processJaringan($limit, $force);
            $results['jaringan'] = $jaringanResults;
            $this->newLine();
        }

        // ================================
        // PROCESS SPK EMBEDDINGS
        // ================================
        if ($type === 'spk' || $type === 'both') {
            $this->info("📋 Processing SPK embeddings...");
            $spkResults = $this->processSpk($limit, $force);
            $results['spk'] = $spkResults;
            $this->newLine();
        }

        // ================================
        // SUMMARY
        // ================================
        $this->info("✅ Regeneration completed!");
        $this->newLine();
        
        $tableData = [];
        
        if ($type === 'jaringan' || $type === 'both') {
            $tableData[] = [
                'Type' => 'JARINGAN',
                'Success' => $results['jaringan']['success'],
                'Failed' => $results['jaringan']['failed'],
                'Total' => $results['jaringan']['success'] + $results['jaringan']['failed']
            ];
        }
        
        if ($type === 'spk' || $type === 'both') {
            $tableData[] = [
                'Type' => 'SPK',
                'Success' => $results['spk']['success'],
                'Failed' => $results['spk']['failed'],
                'Total' => $results['spk']['success'] + $results['spk']['failed']
            ];
        }
        
        if ($type === 'both') {
            $totalSuccess = $results['jaringan']['success'] + $results['spk']['success'];
            $totalFailed = $results['jaringan']['failed'] + $results['spk']['failed'];
            $tableData[] = [
                'Type' => '━━━━━━━━━━',
                'Success' => '━━━━━━━',
                'Failed' => '━━━━━━',
                'Total' => '━━━━━━'
            ];
            $tableData[] = [
                'Type' => 'TOTAL',
                'Success' => $totalSuccess,
                'Failed' => $totalFailed,
                'Total' => $totalSuccess + $totalFailed
            ];
        }
        
        $this->table(
            ['Type', 'Success', 'Failed', 'Total'],
            $tableData
        );

        return Command::SUCCESS;
    }

    /**
     * Process Jaringan embeddings
     */
    private function processJaringan(?int $limit, bool $force): array
    {
        $query = Jaringan::query()->where('is_deleted', false);
        
        if (!$force) {
            $query->whereDoesntHave('embeddings');
        }
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $jaringans = $query->get();
        $total = $jaringans->count();
        
        if ($total === 0) {
            $this->info("✅ No Jaringan records to process");
            return ['success' => 0, 'failed' => 0];
        }
        
        $this->info("Found {$total} Jaringan records");
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($jaringans as $jaringan) {
            try {
                $this->embeddingService->generateJaringanEmbedding($jaringan->no_jaringan);
                $success++;
            } catch (Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("❌ Failed [{$jaringan->no_jaringan}]: {$e->getMessage()}");
            }
            
            $progressBar->advance();
            usleep(100000); // 100ms delay
        }
        
        $progressBar->finish();
        $this->newLine();
        
        return ['success' => $success, 'failed' => $failed];
    }

    /**
     * Process SPK embeddings
     */
    private function processSpk(?int $limit, bool $force): array
    {
        $query = Spk::query()->where('is_deleted', false);
        
        if (!$force) {
            $query->whereDoesntHave('embedding');
        }
        
        if ($limit) {
            $query->limit($limit);
        }
        
        $spks = $query->get();
        $total = $spks->count();
        
        if ($total === 0) {
            $this->info("✅ No SPK records to process");
            return ['success' => 0, 'failed' => 0];
        }
        
        $this->info("Found {$total} SPK records");
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($spks as $spk) {
            try {
                $this->embeddingService->generateSpkEmbedding($spk->id_spk);
                $success++;
            } catch (Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("❌ Failed [{$spk->no_spk}]: {$e->getMessage()}");
            }
            
            $progressBar->advance();
            usleep(100000); // 100ms delay
        }
        
        $progressBar->finish();
        $this->newLine();
        
        return ['success' => $success, 'failed' => $failed];
    }

}