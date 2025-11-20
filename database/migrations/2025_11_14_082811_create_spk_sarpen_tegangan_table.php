<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Sarpen_Tegangan
     * Purpose: Pengukuran tegangan listrik dari berbagai sumber
     * Depends on: spk_sarpen_ruang_server
     * Relationship: One-to-Many (multiple voltage sources per sarpen)
     */
    public function up(): void
    {
        Schema::create('spk_sarpen_tegangan', function (Blueprint $table) {
            // Primary Key
            $table->id('id_tegangan');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_sarpen');

            // Voltage Source Type
            $table->enum('jenis_sumber', [
                'pln',
                'ups',
                'it',
                'generator'
            ]);

            // Voltage Measurements (Phase-Neutral, Phase-Ground, Neutral-Ground)
            $table->decimal('p_n', 6, 2)->nullable();
            $table->decimal('p_g', 6, 2)->nullable();
            $table->decimal('n_g', 6, 2)->nullable();

            // Indexes
            $table->index('id_sarpen');
            $table->unique(['id_sarpen', 'jenis_sumber']); // Prevent duplicate source per sarpen

            // Foreign Key Constraint
            $table->foreign('id_sarpen')
                ->references('id_sarpen')
                ->on('spk_sarpen_ruang_server')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_sarpen_tegangan');
    }
};
