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
                            {--type=spk : Type to regenerate (spk only)}
                            {--limit= : Limit number of records}
                            {--force : Force regenerate}';

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
        $limit = $this->option('limit');
        $force = $this->option('force');

        $this->info("🚀 Starting SPK embeddings regeneration...");
        if ($limit) {
            $this->info("Limit: {$limit}");
        }

        $successCount = 0;
        $failCount = 0;

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
        $this->newLine(2);

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