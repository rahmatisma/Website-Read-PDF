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
        Schema::create('fcwl_perangkat_antenna', function (Blueprint $table) {
            $table->id('id_antenna');
            $table->unsignedBigInteger('id_fcwl')->unique();
            $table->string('polarisasi', 100)->nullable();
            $table->string('altitude', 100)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('antenna_terbounding_dengan_ground', 100)->nullable();
            $table->string('posisi_antena_sejajar', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade');

            // Index
            $table->index('id_fcwl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_perangkat_antenna');
    }
};
