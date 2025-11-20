<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Perizinan_Biaya_Kawasan
     * Purpose: Perizinan dan biaya untuk kawasan private
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_perizinan_biaya_kawasan', function (Blueprint $table) {
            // Primary Key
            $table->id('id_perizinan_kawasan');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Private Area Status (REQUIRED FIELD)
            $table->enum('melewati_kawasan_private', ['ya', 'tidak']);

            // Private Area Details (conditional - if melewati_kawasan_private = 'ya')
            $table->string('nama_kawasan', 255)->nullable();
            $table->string('pic_kawasan', 255)->nullable();
            $table->string('kontak_pic_kawasan', 20)->nullable();

            // Cable Installation in Area
            $table->string('panjang_kabel_dalam_kawasan', 100)->nullable();
            $table->string('pelaksana_penarikan_kabel_dalam_kawasan', 255)->nullable();

            // Costs
            $table->string('deposit_kerja', 255)->nullable();
            $table->string('supervisi', 255)->nullable();
            $table->string('biaya_penarikan_kabel_dalam_kawasan', 255)->nullable();
            $table->string('biaya_sewa', 255)->nullable();
            $table->string('biaya_lain', 255)->nullable();

            // Additional Information
            $table->text('info_lain_lain_jika_ada')->nullable();

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
        Schema::dropIfExists('spk_perizinan_biaya_kawasan');
    }
};
