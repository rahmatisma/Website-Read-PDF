<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: Berita_Acara
     * Purpose: Berita acara penyelesaian SPK
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('berita_acara', function (Blueprint $table) {
            // Primary Key
            $table->id('id_berita_acara');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // BA Information
            $table->string('judul_spk', 255)->default('BERITA ACARA');

            // Timestamps
            $table->timestamps();

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
        Schema::dropIfExists('berita_acara');
    }
};
