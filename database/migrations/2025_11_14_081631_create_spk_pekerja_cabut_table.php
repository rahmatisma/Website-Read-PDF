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
        Schema::create('spk_pekerja_cabut', function (Blueprint $table) {
            $table->id('id_pekerja');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('pic_pelanggan', 255)->nullable();
            $table->string('kontak_pic_pelanggan', 50)->nullable();
            $table->string('teknisi', 255)->nullable();
            $table->string('nama_vendor', 255)->nullable();

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
        Schema::dropIfExists('spk_pekerja_cabut');
    }
};
