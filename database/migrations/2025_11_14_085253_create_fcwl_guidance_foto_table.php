<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Guidance_Foto
     * Purpose: Foto panduan untuk form wireless (berbagai jenis foto)
     * Depends on: form_checklist_wireless
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('fcwl_guidance_foto', function (Blueprint $table) {
            // Primary Key
            $table->id('id_guidance');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcwl');

            // Photo Type - sesuai dengan schema yang benar
            $table->enum('jenis_foto', [
                'teknisi_aktivasi',
                'kondisi_sebelum_perbaikan',
                'action_perbaikan',
                'kondisi_setelah_perbaikan',
                'test_ping',
                'catuan_listrik',
                'indikator_perangkat',
                'kondisi_rak_penempatan',
                'antenna_installation',
                'outdoor_mounting',
                'guidance_umum'  //  Ditambahkan sesuai mapJenisFoto()
            ]);

            // Photo Path
            $table->string('path_foto', 1000);

            //  TAMBAHAN: Kolom urutan (PENTING!)
            $table->integer('urutan')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcwl');
            $table->index(['id_fcwl', 'jenis_foto']);

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
        Schema::dropIfExists('fcwl_guidance_foto');
    }
};