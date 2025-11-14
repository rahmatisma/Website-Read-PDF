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
        Schema::create('spk_perizinan_biaya_kawasan', function (Blueprint $table) {
            $table->id('id_perizinan_kawasan');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('melewati_kawasan_private', 100)->nullable();
            $table->string('nama_kawasan', 255)->nullable();
            $table->string('pic_kawasan', 255)->nullable();
            $table->string('kontak_pic_kawasan', 50)->nullable();
            $table->string('panjang_kabel_dalam_kawasan', 100)->nullable();
            $table->string('pelaksana_penarikan_kabel_dalam_kawasan', 255)->nullable();
            $table->string('deposit_kerja', 255)->nullable();
            $table->string('supervisi', 255)->nullable();
            $table->string('biaya_penarikan_kabel_dalam_kawasan', 255)->nullable();
            $table->string('biaya_sewa', 255)->nullable();
            $table->string('biaya_lain', 255)->nullable();
            $table->text('info_lain_lain_jika_ada')->nullable();

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
        Schema::dropIfExists('spk_perizinan_biaya_kawasan');
    }
};
