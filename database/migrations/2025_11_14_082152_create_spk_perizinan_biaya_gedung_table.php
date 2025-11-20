<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Perizinan_Biaya_Gedung
     * Purpose: Informasi perizinan dan biaya terkait gedung
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_perizinan_biaya_gedung', function (Blueprint $table) {
            // Primary Key
            $table->id('id_perizinan_gedung');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Building Management Contact
            $table->string('pic_bm', 255)->nullable();
            $table->string('kontak_pic_bm', 20)->nullable();

            // Material & Infrastructure
            $table->string('material_dan_infrastruktur', 255)->nullable();
            $table->string('panjang_kabel_dalam_gedung', 100)->nullable();

            // Cable Installation
            $table->string('pelaksana_penarikan_kabel_dalam_gedung', 255)->nullable();
            $table->string('waktu_pelaksanaan_penarikan_kabel', 255)->nullable();

            // Supervision & Costs
            $table->string('supervisi', 255)->nullable();
            $table->string('deposit_kerja', 255)->nullable();
            $table->string('ikg_instalasi_kabel_gedung', 255)->nullable();
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
        Schema::dropIfExists('spk_perizinan_biaya_gedung');
    }
};
