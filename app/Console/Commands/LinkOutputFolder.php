<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LinkOutputFolder extends Command
{
    protected $signature = 'storage:link-output';
    protected $description = 'Create symlink from public/output to storage/app/output';

    public function handle()
    {
        $target = storage_path('app/output');
        $link = public_path('output');

        // Pastikan folder target exists
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info(" Created directory: {$target}");
        }

        // Hapus symlink lama kalau ada
        if (File::exists($link)) {
            if (is_link($link)) {
                unlink($link);
                $this->info("🗑️ Removed old symlink");
            } else {
                $this->error("{$link} already exists and is not a symlink!");
                return 1;
            }
        }

        // Buat symlink baru
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: gunakan mklink
            $target = str_replace('/', '\\', $target);
            $link = str_replace('/', '\\', $link);
            exec("mklink /D \"{$link}\" \"{$target}\"", $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info(" Symlink created: {$link} -> {$target}");
                return 0;
            } else {
                $this->error("Failed to create symlink. Run as Administrator!");
                return 1;
            }
        } else {
            // Linux/Mac: gunakan symlink()
            if (symlink($target, $link)) {
                $this->info(" Symlink created: {$link} -> {$target}");
                return 0;
            } else {
                $this->error("Failed to create symlink!");
                return 1;
            }
        }
    }
}