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
        Schema::create('pelaksanaan', function (Blueprint $table) {
            $table->id('id_pelaksanaan');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->dateTime('waktu_permintaan')->nullable();
            $table->dateTime('waktu_datang')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaan');
    }
};
