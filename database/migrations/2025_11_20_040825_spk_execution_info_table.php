<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Execution_Info
     * Purpose: Informasi eksekusi SPK (lokasi GPS, PIC, teknisi, vendor)
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_execution_info', function (Blueprint $table) {
            // Primary Key
            $table->id('id_execution');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Location (GPS Coordinates)
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            // Customer Contact Person
            $table->string('pic_pelanggan', 255)->nullable();
            $table->string('kontak_pic_pelanggan', 20)->nullable();

            // Execution Team
            $table->string('teknisi', 255);
            $table->string('nama_vendor', 255);

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
        Schema::dropIfExists('spk_execution_info');
    }
};
