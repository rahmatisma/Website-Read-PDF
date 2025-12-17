<?php

namespace App\Console\Commands;

use App\Services\EmbeddingService;
use App\Models\Jaringan;
use App\Models\Spk;
use Illuminate\Console\Command;
use Exception;

class RegenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'embeddings:regenerate 
                            {--type=both : Type to regenerate (jaringan, spk, both)}
                            {--limit= : Limit number of records to process}
                            {--force : Force regenerate even if embedding exists}';

    /**
     * The console command description.
     */
    protected $description = 'Regenerate embeddings for Jaringan and SPK data';

    protected EmbeddingService $embeddingService;

    /**
     * Create a new command instance.
     */
    public function __construct(EmbeddingService $embeddingService)
    {
        parent::__construct();
        $this->embeddingService = $embeddingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $limit = $this->option('limit');
        $force = $this->option('force');

        $this->info("🚀 Starting embeddings regeneration...");
        $this->info("Type: {$type}");
        if ($limit) {
            $this->info("Limit: {$limit}");
        }

        $successCount = 0;
        $failCount = 0;

        // Regenerate Jaringan Embeddings
        if (in_array($type, ['jaringan', 'both'])) {
            $this->info("\n📊 Processing JARINGAN embeddings...");
            
            $query = Jaringan::query()->where('is_deleted', false);
            
            if (!$force) {
                $query->whereDoesntHave('embedding');
            }
            
            if ($limit) {
                $query->limit($limit);
            }
            
            $jarinnganRecords = $query->get();
            $total = $jarinnganRecords->count();
            
            $this->info("Found {$total} records to process");
            
            $progressBar = $this->output->createProgressBar($total);
            $progressBar->start();
            
            foreach ($jarinnganRecords as $jaringan) {
                try {
                    $this->embeddingService->generateJaringanEmbedding($jaringan->no_jaringan);
                    $successCount++;
                } catch (Exception $e) {
                    $failCount++;
                    $this->error("\nFailed: {$jaringan->no_jaringan} - {$e->getMessage()}");
                }
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
        }

        // Regenerate SPK Embeddings
        if (in_array($type, ['spk', 'both'])) {
            $this->info("\n📊 Processing SPK embeddings...");
            
            $query = Spk::query()->where('is_deleted', false);
            
            if (!$force) {
                $query->whereDoesntHave('embedding');
            }
            
            if ($limit) {
                $query->limit($limit);
            }
            
            $spkRecords = $query->get();
            $total = $spkRecords->count();
            
            $this->info("Found {$total} records to process");
            
            $progressBar = $this->output->createProgressBar($total);
            $progressBar->start();
            
            foreach ($spkRecords as $spk) {
                try {
                    $this->embeddingService->generateSpkEmbedding($spk->id_spk);
                    $successCount++;
                } catch (Exception $e) {
                    $failCount++;
                    $this->error("\nFailed: {$spk->no_spk} - {$e->getMessage()}");
                }
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info("✅ Regeneration completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failCount],
                ['Total', $successCount + $failCount],
            ]
        );

        return Command::SUCCESS;
    }
}