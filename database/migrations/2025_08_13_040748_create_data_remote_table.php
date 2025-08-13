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
        Schema::create('data_remote', function (Blueprint $table) {
            $table->id('id_remote');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->dateTime('jam_perintah')->nullable();
            $table->dateTime('jam_persiapan')->nullable();
            $table->dateTime('jam_berangkat')->nullable();
            $table->dateTime('jam_tiba_lokasi')->nullable();
            $table->dateTime('jam_mulai_kerja')->nullable();
            $table->dateTime('jam_selesai_kerja')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->dateTime('jam_tiba_kantor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_remote');
    }
};
