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
        Schema::create('dokumentasi', function (Blueprint $table) {
            $table->id('id_dokumentasi');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->text('deskripsi_foto')->nullable();
            $table->string('path_foto', 255);
            $table->dateTime('tanggal_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasi');
    }
};
