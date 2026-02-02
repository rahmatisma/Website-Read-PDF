<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // TABEL 1: JARINGAN EMBEDDINGS
        // ============================================
        Schema::create('jaringan_embeddings', function (Blueprint $table) {
            $table->id('id_embedding');
            $table->string('no_jaringan', 100); //  HAPUS ->unique()
            
            // Content text yang di-embed (untuk reference)
            $table->text('content_text');
            
            // Embedding vector (disimpan sebagai JSON array untuk MySQL)
            // Format: [0.123, -0.456, 0.789, ...]
            $table->json('embedding');
            
            // Model yang digunakan untuk generate embedding
            $table->string('embedding_model', 100)->default('nomic-embed-text');
            
            // Dimensi vector (384 untuk nomic-embed-text)
            $table->integer('embedding_dimension')->default(384);
            
            $table->timestamps();
            
            // Foreign key ke tabel JARINGAN
            $table->foreign('no_jaringan')
                ->references('no_jaringan')
                ->on('jaringan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Index untuk performance (BUKAN unique)
            $table->index('no_jaringan');
            $table->index('created_at');
        });
        
        // ============================================
        // TABEL 2: SPK EMBEDDINGS
        // ============================================
        Schema::create('spk_embeddings', function (Blueprint $table) {
            $table->id('id_embedding');
            $table->unsignedBigInteger('id_spk');
            $table->string('no_spk', 100);
            
            // Content text yang di-embed (untuk reference)
            $table->text('content_text');
            
            // Embedding vector (disimpan sebagai JSON array)
            $table->json('embedding');
            
            // Model yang digunakan
            $table->string('embedding_model', 100)->default('nomic-embed-text');
            
            // Dimensi vector
            $table->integer('embedding_dimension')->default(384);
            
            $table->timestamps();
            
            // Foreign key ke tabel SPK
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Index untuk performance (BUKAN unique)
            $table->index('id_spk');
            $table->index('no_spk');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_embeddings');
        Schema::dropIfExists('jaringan_embeddings');
    }
};