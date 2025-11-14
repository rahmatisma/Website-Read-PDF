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
        Schema::create('spk_vendor', function (Blueprint $table) {
            $table->id('id_vendor_detail');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
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
        Schema::dropIfExists('spk_vendor');
    }
};
