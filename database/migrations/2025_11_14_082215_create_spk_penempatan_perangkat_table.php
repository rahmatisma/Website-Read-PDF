<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Penempatan_Perangkat
     * Purpose: Informasi penempatan modem dan router
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_penempatan_perangkat', function (Blueprint $table) {
            // Primary Key
            $table->id('id_penempatan');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Equipment Placement
            $table->text('lokasi_penempatan_modem_dan_router')->nullable();

            // Server Room Readiness
            $table->enum('kesiapan_ruang_server', ['siap', 'tidak_siap'])->nullable();
            $table->enum('ketersedian_rak_server', ['ada', 'tidak_ada'])->nullable();
            $table->enum('space_modem_dan_router', ['ada', 'tidak_ada'])->nullable();

            // Photography Permission
            $table->enum('diizinkan_foto_ruang_server_pelanggan', ['ya', 'tidak'])->nullable();

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
        Schema::dropIfExists('spk_penempatan_perangkat');
    }
};
