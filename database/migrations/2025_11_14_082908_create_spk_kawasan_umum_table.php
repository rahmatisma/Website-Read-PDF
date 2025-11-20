<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Kawasan_Umum
     * Purpose: Informasi kawasan umum yang dilewati jalur kabel
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_kawasan_umum', function (Blueprint $table) {
            // Primary Key
            $table->id('id_kawasan_umum');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Public Area Information
            $table->text('nama_kawasan_umum_pu_yang_dilewati')->nullable();
            $table->string('panjang_jalur_outdoor_di_kawasan_umum', 100)->nullable();

            // Index
            $table->index('id_spk');

            // Foreign Key Constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
