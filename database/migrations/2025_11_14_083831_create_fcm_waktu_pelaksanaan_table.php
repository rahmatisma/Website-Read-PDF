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

            // Foreign key constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'jenis_waktu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_waktu_pelaksanaan');
    }
};
