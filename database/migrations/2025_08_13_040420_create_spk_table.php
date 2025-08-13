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
            $table->string('no_spk', 50);
            $table->date('tanggal_spk');
            $table->string('tipe_spk', 50); // INSTALL, DISMANTLE, dll.
            $table->foreignId('id_jaringan')->constrained('jaringan','id_jaringan')->cascadeOnDelete();
            $table->string('no_mr', 50)->nullable();
            $table->string('no_fps', 50)->nullable();
            $table->foreignId('id_vendor')->constrained('vendor','id_vendor')->cascadeOnDelete();
            $table->timestamps();
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
