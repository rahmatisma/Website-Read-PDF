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
        Schema::create('spk_lokasi_antena', function (Blueprint $table) {
            $table->id('id_lokasi_antena');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('lokasi_antena', 255)->nullable();
            $table->text('detail_lokasi_antena')->nullable();
            $table->string('space_tersedia', 100)->nullable();
            $table->string('akses_di_lokasi_perlu_alat_bantu', 255)->nullable();
            $table->string('penangkal_petir', 100)->nullable();
            $table->string('tinggi_penangkal_petir', 100)->nullable();
            $table->string('jarak_ke_lokasi_antena', 100)->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('tower_pole', 100)->nullable();
            $table->string('pemilik_tower_pole', 255)->nullable();

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
        Schema::dropIfExists('spk_lokasi_antena');
    }
};
