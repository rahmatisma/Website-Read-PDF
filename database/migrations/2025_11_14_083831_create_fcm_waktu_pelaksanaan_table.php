<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCW_Waktu_Pelaksanaan
     * Purpose: Timeline pelaksanaan wireline (8 timestamps)
     * Depends on: form_checklist_wireline
     * Relationship: ONE-TO-MANY (max 8 records per form)
     */
    public function up(): void
    {
        Schema::create('fcw_waktu_pelaksanaan', function (Blueprint $table) {
            // Primary Key
            $table->id('id_waktu');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcw');

            // Time Type (8 types)
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

            // Timestamp
            $table->timestamp('waktu');

            // Indexes
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'jenis_waktu']); // Prevent duplicate time type per form

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
        Schema::dropIfExists('fcw_waktu_pelaksanaan');
    }
};
