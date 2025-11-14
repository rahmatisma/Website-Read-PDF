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
        Schema::create('spk_hh_baru', function (Blueprint $table) {
            $table->id('id_hh_baru');
            $table->unsignedBigInteger('id_spk');
            $table->integer('nomor_hh');
            $table->text('lokasi_hh')->nullable();
            $table->string('longitude_dan_latitude_hh', 255)->nullable();
            $table->string('kebutuhan_penambahan_closure', 100)->nullable();
            $table->string('kapasitas_closure', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_spk');
            $table->index(['id_spk', 'nomor_hh']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_hh_baru');
    }
};
