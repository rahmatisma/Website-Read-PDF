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
        Schema::create('spk_penempatan_perangkat', function (Blueprint $table) {
            $table->id('id_penempatan');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->text('lokasi_penempatan_modem_dan_router')->nullable();
            $table->string('kesiapan_ruang_server', 100)->nullable();
            $table->string('ketersedian_rak_server', 100)->nullable();
            $table->string('space_modem_dan_router', 100)->nullable();
            $table->string('diizinkan_foto_ruang_server_pelanggan', 100)->nullable();

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
        Schema::dropIfExists('spk_penempatan_perangkat');
    }
};
