<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcw_waktu_pelaksanaan', function (Blueprint $table) {
            $table->id('id_waktu');
            $table->unsignedBigInteger('id_fcw');
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
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'jenis_waktu']);

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
        Schema::dropIfExists('fcw_waktu_pelaksanaan');
    }
};