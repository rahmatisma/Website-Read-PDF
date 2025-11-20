<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Indoor_Area
     * Purpose: Data area indoor untuk wireless
     * Depends on: form_checklist_wireless
     */
    public function up(): void
    {
        Schema::create('fcwl_indoor_area', function (Blueprint $table) {
            // Primary Key
            $table->id('id_indoor');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_fcwl')->unique();

            // UPS Information
            $table->string('merk_ups', 100)->nullable();
            $table->string('kapasitas_ups', 100)->nullable();
            $table->string('jenis_ups', 100)->nullable();

            // Room Conditions
            $table->enum('ruangan_bebas_debu', ['ya', 'tidak'])->nullable();
            $table->decimal('suhu_ruangan', 4, 1)->nullable();
            $table->enum('terpasang_ground_bar', ['ya', 'tidak'])->nullable();

            // Modem Power Input
            $table->string('catuan_input_modem', 100)->nullable();
            $table->decimal('v_input_modem_p_n', 6, 2)->nullable();
            $table->decimal('v_input_modem_n_g', 6, 2)->nullable();

            // Modem Placement
            $table->enum('bertumpuk', ['ya', 'tidak'])->nullable();
            $table->string('lokasi_ruang', 255)->nullable();
            $table->decimal('suhu_casing_modem', 4, 1)->nullable();

            // Cabling
            $table->enum('catuan_input_terbounding', ['ya', 'tidak'])->nullable();
            $table->string('splicing_konektor_kabel', 100)->nullable();

            // CPE Information
            $table->string('pemilik_perangkat_cpe', 100)->nullable();
            $table->string('jenis_perangkat_cpe', 100)->nullable();

            // Index
            $table->index('id_fcwl');

            // Foreign Key Constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
