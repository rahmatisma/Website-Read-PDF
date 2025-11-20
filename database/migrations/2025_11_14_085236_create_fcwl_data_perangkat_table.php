<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Data_Perangkat
     * Purpose: Data perangkat untuk form wireless (multiple devices)
     * Depends on: form_checklist_wireless
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('fcwl_data_perangkat', function (Blueprint $table) {
            // Primary Key
            $table->id('id_perangkat');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcwl');

            // Device Category
            $table->enum('kategori', [
                'existing',
                'tidak_terpakai',
                'cabut',
                'pengganti_pasang_baru'
            ]);

            // Device Information
            $table->string('nama_barang', 255);
            $table->string('no_reg', 100)->nullable();
            $table->string('serial_number', 100)->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcwl');
            $table->index('kategori');

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
        Schema::dropIfExists('fcwl_data_perangkat');
    }
};
