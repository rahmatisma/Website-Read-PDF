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
        Schema::create('spk_data_splitter', function (Blueprint $table) {
            $table->id('id_splitter');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('lokasi_splitter', 255)->nullable();
            $table->string('id_splitter_text', 100)->nullable();
            $table->string('kapasitas_splitter', 100)->nullable();
            $table->string('jumlah_port_kosong', 100)->nullable();
            $table->text('list_port_kosong_dan_redaman')->nullable();
            $table->string('nama_node_jika_tidak_ada_splitter', 255)->nullable();
            $table->text('list_port_kosong')->nullable();
            $table->string('arah_akses', 255)->nullable();

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
        Schema::dropIfExists('spk_data_splitter');
    }
};
