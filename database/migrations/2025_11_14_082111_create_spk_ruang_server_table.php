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
        Schema::create('spk_sarpen_ruang_server', function (Blueprint $table) {
            $table->id('id_sarpen');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('power_line_listrik', 255)->nullable();
            $table->string('ketersediaan_power_outlet', 255)->nullable();
            $table->string('grounding_listrik', 100)->nullable();
            $table->string('ups', 100)->nullable();
            $table->string('ruangan_ber_ac', 100)->nullable();
            $table->string('suhu_ruangan', 100)->nullable();
            $table->string('lantai', 100)->nullable();
            $table->string('ruang', 255)->nullable();
            $table->string('perangkat_pelanggan', 255)->nullable();

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
        Schema::dropIfExists('spk_sarpen_ruang_server');
    }
};
