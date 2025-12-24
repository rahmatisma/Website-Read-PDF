<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcwl_waktu_pelaksanaan', function (Blueprint $table) {
            $table->id('id_waktu');
            $table->unsignedBigInteger('id_fcwl');
            $table->enum('jenis_waktu', [
                'perintah',
                'persiapan',
                'berangkat',
                'tiba_lokasi',
                'mulai_kerja',
                'selesai_kerja',
                'pulang',
                'tiba_kantor'
            ]);
            $table->timestamp('waktu');
            $table->text('keterangan')->nullable();

            // Indexes
            $table->index('id_fcwl');
            $table->unique(['id_fcwl', 'jenis_waktu']);

            // Foreign Key
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcwl_waktu_pelaksanaan');
    }
};
