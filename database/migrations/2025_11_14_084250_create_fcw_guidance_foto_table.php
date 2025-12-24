<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcw_guidance_foto', function (Blueprint $table) {
            $table->id('id_guidance');
            $table->unsignedBigInteger('id_fcw');
            $table->enum('jenis_foto', [
                'teknisi_aktivasi',
                'kondisi_sebelum_perbaikan',
                'action_perbaikan',
                'kondisi_setelah_perbaikan',
                'test_ping',
                'catuan_listrik',
                'indikator_perangkat',
                'kondisi_rak_penempatan',
                'guidance_umum'
            ]);
            $table->string('path_foto', 1000);
            $table->integer('urutan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcw');
            $table->index(['id_fcw', 'jenis_foto']);

            // Foreign Key
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcw_guidance_foto');
    }
};