<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_HH_Baru
     * Purpose: Data handhole/manhole baru yang perlu dibuat (multiple records per SPK)
     * Depends on: spk
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('spk_hh_baru', function (Blueprint $table) {
            // Primary Key
            $table->id('id_hh_baru');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_spk');

            // HH Number (sequence within SPK)
            $table->integer('nomor_hh');

            // HH Location
            $table->text('lokasi_hh')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            // Closure Requirements
            $table->string('kebutuhan_penambahan_closure', 100)->nullable();
            $table->string('kapasitas_closure', 100)->nullable();

            // Indexes
            $table->index('id_spk');
            $table->index(['id_spk', 'nomor_hh']);

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
        Schema::dropIfExists('spk_hh_baru');
    }
};
