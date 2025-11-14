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
        Schema::create('spk_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('nama_pelanggan', 255)->nullable();
            $table->text('lokasi_pelanggan')->nullable();
            $table->string('kontak_person', 255)->nullable();
            $table->string('telepon', 50)->nullable();

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
        Schema::dropIfExists('spk_pelanggan');
    }
};
