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
        Schema::create('spk_jaringan', function (Blueprint $table) {
            $table->id('id_jaringan');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('no_jaringan', 100)->nullable();
            $table->string('jasa', 100)->nullable();
            $table->string('manage_router', 255)->nullable();
            $table->string('opsi_router', 255)->nullable();
            $table->string('ip_lan', 100)->nullable();
            $table->date('tgl_rfs_la')->nullable();
            $table->date('tgl_rfs_plg')->nullable();
            $table->string('kode_jaringan', 100)->nullable();
            $table->string('no_fmb', 100)->nullable();
            $table->string('jenis_aktivasi', 100)->nullable();
            $table->string('jenis_instalasi', 100)->nullable();
            $table->string('media_akses', 100)->nullable();
            $table->text('pop')->nullable();
            $table->string('kecepatan', 255)->nullable();

            // Foreign key constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade');

            // Additional indexes
            $table->index('id_spk');
            $table->index('no_jaringan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_jaringan');
    }
};
