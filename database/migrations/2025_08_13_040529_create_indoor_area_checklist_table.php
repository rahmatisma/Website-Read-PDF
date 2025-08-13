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
        Schema::create('indoor_area_checklist', function (Blueprint $table) {
            $table->id('id_indoor');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->string('perangkat_modem', 100);   // ex: Indikator Modem, Modem FO
            $table->string('quality_parameter', 100); // ex: POWER, LINK-WAN, Optical LED
            $table->text('standard');
            $table->text('nms_engineer')->nullable();
            $table->text('onsite_teknisi')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('hasil_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indoor_area_checklist');
    }
};
