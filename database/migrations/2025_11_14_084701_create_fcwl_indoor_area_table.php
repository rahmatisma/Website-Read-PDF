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
        Schema::create('fcwl_indoor_area', function (Blueprint $table) {
            $table->id('id_indoor');
            $table->unsignedBigInteger('id_fcwl')->unique();
            $table->string('merk_ups', 100)->nullable();
            $table->string('kapasitas_ups', 100)->nullable();
            $table->string('jenis_ups', 100)->nullable();
            $table->string('ruangan_bebas_debu', 100)->nullable();
            $table->string('suhu_ruangan', 100)->nullable();
            $table->string('terpasang_ground_bar', 100)->nullable();
            $table->string('catuan_input_modem', 100)->nullable();
            $table->string('v_input_modem_p_n', 50)->nullable();
            $table->string('v_input_modem_n_g', 50)->nullable();
            $table->string('bertumpuk', 100)->nullable();
            $table->string('lokasi_ruang', 255)->nullable();
            $table->string('suhu_casing_modem', 100)->nullable();
            $table->string('catuan_input_terbounding', 100)->nullable();
            $table->string('splicing_konektor_kabel', 100)->nullable();
            $table->string('pemilik_perangkat_cpe', 100)->nullable();
            $table->string('jenis_perangkat_cpe', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade');

            // Index
            $table->index('id_fcwl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_indoor_area');
    }
};
