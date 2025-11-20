<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Sarpen_Ruang_Server
     * Purpose: Survey sarana penunjang ruang server
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_sarpen_ruang_server', function (Blueprint $table) {
            // Primary Key
            $table->id('id_sarpen');
            
            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();
            
            // Power & Electrical
            $table->string('power_line_listrik', 255)->nullable();
            $table->string('ketersediaan_power_outlet', 255)->nullable();
            $table->enum('grounding_listrik', ['ada', 'tidak_ada'])->nullable();
            $table->enum('ups', ['tersedia', 'tidak_tersedia'])->nullable();
            
            // Room Conditions
            $table->enum('ruangan_ber_ac', ['ada', 'tidak_ada'])->nullable();
            $table->decimal('suhu_ruangan_value', 4, 1)->nullable();
            $table->string('suhu_ruangan_keterangan', 100)->nullable();
            
            // Room Location
            $table->string('lantai', 100)->nullable();
            $table->string('ruang', 255)->nullable();
            
            // Equipment
            $table->string('perangkat_pelanggan', 255)->nullable();
            
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
        Schema::dropIfExists('spk_sarpen_ruang_server');
    }
};