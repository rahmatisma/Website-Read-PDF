<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCW_Guidance_Foto
     * Purpose: Foto panduan untuk form wireline (8 jenis foto)
     * Depends on: form_checklist_wireline
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('fcw_guidance_foto', function (Blueprint $table) {
            // Primary Key
            $table->id('id_guidance');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcw');

            // Photo Type (8 types for wireline)
            $table->enum('jenis_foto', [
                'teknisi_aktivasi',
                'kondisi_sebelum_perbaikan',
                'action_perbaikan',
                'kondisi_setelah_perbaikan',
                'test_ping',
                'catuan_listrik',
                'indikator_perangkat',
                'kondisi_rak_penempatan'
            ]);

            // Photo Path
            $table->string('path_foto', 1000);

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcw');
            $table->index(['id_fcw', 'jenis_foto']);

            // Foreign Key Constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_guidance_foto');
    }
};
