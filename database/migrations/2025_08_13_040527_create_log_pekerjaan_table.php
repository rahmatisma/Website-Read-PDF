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
        Schema::create('log_pekerjaan', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->dateTime('waktu');
            $table->text('aktivitas');
            $table->string('pelaksana', 100);
            $table->text('keterangan')->nullable();
            $table->string('path_foto', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_pekerjaan');
    }
};
