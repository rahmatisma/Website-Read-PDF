<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Informasi_Gedung
     * Purpose: Informasi detail gedung/bangunan pelanggan
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_informasi_gedung', function (Blueprint $table) {
            // Primary Key
            $table->id('id_info_gedung');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Building Address & Status
            $table->text('alamat');
            $table->string('status_gedung', 100)->nullable();
            $table->string('kondisi_gedung', 100)->nullable();

            // Building Owner Information
            $table->string('pemilik_bangunan', 255)->nullable();
            $table->string('kontak_person', 255)->nullable();
            $table->string('bagian_jabatan', 255)->nullable();
            $table->string('telpon_fax', 20)->nullable();
            $table->string('email', 320)->nullable();

            // Building Specifications
            $table->integer('jumlah_lantai_gedung')->nullable();
            $table->string('pelanggan_fo', 100)->nullable();

            // Antenna & Installation
            $table->string('penempatan_antena', 100)->nullable();
            $table->string('sewa_space_antena', 100)->nullable();
            $table->string('sewa_shaft_kabel', 100)->nullable();

            // Cost Information
            $table->string('biaya_ikg', 100)->nullable();
            $table->string('penanggungjawab_sewa', 255)->nullable();

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
        Schema::dropIfExists('spk_informasi_gedung');
    }
};
