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
        Schema::create('spk_informasi_gedung', function (Blueprint $table) {
            $table->id('id_info_gedung');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->text('alamat')->nullable();
            $table->string('status_gedung', 100)->nullable();
            $table->string('kondisi_gedung', 100)->nullable();
            $table->string('pemilik_bangunan', 255)->nullable();
            $table->string('kontak_person', 255)->nullable();
            $table->string('bagian_jabatan', 255)->nullable();
            $table->string('telpon_fax', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->integer('jumlah_lantai_gedung')->nullable();
            $table->string('pelanggan_fo', 100)->nullable();
            $table->string('penempatan_antena', 100)->nullable();
            $table->string('sewa_space_antena', 100)->nullable();
            $table->string('sewa_shaft_kabel', 100)->nullable();
            $table->string('biaya_ikg', 100)->nullable();
            $table->string('penanggungjawab_sewa', 255)->nullable();

            // Foreign key constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade');

            // Index
            $table->index('id_spk');
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
