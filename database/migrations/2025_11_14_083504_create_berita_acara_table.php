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
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id('id_berita_acara');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('judul_spk', 255)->nullable();
            $table->string('tipe_spk', 100)->nullable();
            $table->string('nomor_spk', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('no_fps', 100)->nullable();
            $table->string('jenis_aktivasi', 100)->nullable();
            $table->string('jenis_instalasi', 100)->nullable();
            $table->string('media_akses', 100)->nullable();
            $table->text('pop')->nullable();
            $table->string('kecepatan', 255)->nullable();
            $table->string('kontak_person', 255)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->timestamp('permintaan_pelanggan')->nullable();
            $table->timestamp('datang')->nullable();
            $table->timestamp('selesai')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('berita_acara');
    }
};
