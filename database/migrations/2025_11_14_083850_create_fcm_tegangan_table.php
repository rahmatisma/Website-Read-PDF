<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCW_Tegangan
     * Purpose: Pengukuran tegangan listrik untuk form wireline
     * Depends on: form_checklist_wireline
     * Relationship: ONE-TO-MANY (max 4 records - 4 voltage sources)
     */
    public function up(): void
    {
        Schema::create('fcw_tegangan', function (Blueprint $table) {
            // Primary Key
            $table->id('id_tegangan');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcw');

            // Voltage Source Type
            $table->enum('jenis_sumber', [
                'pln',
                'ups',
                'it',
                'generator'
            ]);

            // Voltage Measurements (Phase-Neutral, Phase-Ground, Neutral-Ground)
            $table->decimal('p_n', 6, 2)->nullable();
            $table->decimal('p_g', 6, 2)->nullable();
            $table->decimal('n_g', 6, 2)->nullable();

            // Indexes
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'jenis_sumber']); // Prevent duplicate source per form

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
        Schema::dropIfExists('fcw_tegangan');
    }
};
