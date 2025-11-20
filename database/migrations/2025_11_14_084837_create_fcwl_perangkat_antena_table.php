<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Perangkat_Antenna
     * Purpose: Data perangkat antenna untuk wireless
     * Depends on: form_checklist_wireless
     */
    public function up(): void
    {
        Schema::create('fcwl_perangkat_antena', function (Blueprint $table) {
            // Primary Key
            $table->id('id_antena');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_fcwl')->unique();

            // Antenna Information
            $table->string('polarisasi', 100)->nullable();
            $table->string('altitude', 100)->nullable();
            $table->string('lokasi', 255)->nullable();

            // Antenna Positioning
            $table->enum('antena_terbounding_dengan_ground', ['ya', 'tidak'])->nullable();
            $table->enum('posisi_antena_sejajar', ['ya', 'tidak'])->nullable();

            // Index
            $table->index('id_fcwl');

            // Foreign Key Constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_perangkat_antena');
    }
};
