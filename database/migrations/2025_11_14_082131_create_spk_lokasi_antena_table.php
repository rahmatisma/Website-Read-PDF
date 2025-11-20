<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Lokasi_Antena
     * Purpose: Informasi lokasi dan kondisi antena
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_lokasi_antena', function (Blueprint $table) {
            // Primary Key
            $table->id('id_lokasi_antena');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Antenna Location
            $table->string('lokasi_antena', 255)->nullable();
            $table->text('detail_lokasi_antena')->nullable();
            $table->string('space_tersedia', 100)->nullable();
            $table->string('akses_di_lokasi_perlu_alat_bantu', 255)->nullable();

            // Lightning Protection
            $table->enum('penangkal_petir', ['ada', 'tidak_ada'])->nullable();
            $table->string('tinggi_penangkal_petir', 100)->nullable();
            $table->string('jarak_ke_lokasi_antena', 100)->nullable();

            // Follow-up Action
            $table->text('tindak_lanjut')->nullable();

            // Tower/Pole Information
            $table->enum('tower_pole', ['ada', 'tidak_ada'])->nullable();
            $table->string('pemilik_tower_pole', 255)->nullable();

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
        Schema::dropIfExists('spk_lokasi_antena');
    }
};
