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
        Schema::create('spk_kawasan_umum', function (Blueprint $table) {
            $table->id('id_kawasan_umum');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->text('nama_kawasan_umum_pu_yang_dilewati')->nullable();
            $table->string('panjang_jalur_outdoor_di_kawasan_umum', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade');

            // Index
            $table->index('id_spk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_kawasan_umum');
    }
};
