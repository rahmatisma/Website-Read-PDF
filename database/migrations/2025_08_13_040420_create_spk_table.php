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
        Schema::create('spk', function (Blueprint $table) {
            $table->id('id_spk');
            $table->string('document_type', 50)->nullable();
            $table->enum('jenis_spk', ['aktivasi', 'dismantle', 'instalasi', 'survey']);
            $table->string('no_spk', 100)->unique();
            $table->date('tanggal_spk')->nullable();
            $table->string('no_mr', 100)->nullable();
            $table->string('no_fps', 100)->nullable();
            $table->timestamps();
            
            // Additional indexes
            $table->index('jenis_spk');
            $table->index('tanggal_spk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk');
    }
};