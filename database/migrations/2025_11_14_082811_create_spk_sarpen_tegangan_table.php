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
        Schema::create('spk_sarpen_tegangan', function (Blueprint $table) {
            $table->id('id_tegangan');
            $table->unsignedBigInteger('id_sarpen');
            $table->enum('jenis_sumber', ['pln', 'ups', 'it', 'generator']);
            $table->string('p_n', 50)->nullable();
            $table->string('p_g', 50)->nullable();
            $table->string('n_g', 50)->nullable();

            // Foreign key constraint
            $table->foreign('id_sarpen')
                ->references('id_sarpen')
                ->on('spk_sarpen_ruang_server')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_sarpen');
            $table->unique(['id_sarpen', 'jenis_sumber']);
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
