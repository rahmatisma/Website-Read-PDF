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
        Schema::create('form_checklist_wireline', function (Blueprint $table) {
            $table->id('id_fcw');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('no_spk', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('posisi_modem_di_lt', 100)->nullable();
            $table->string('ruang', 100)->nullable();
            $table->string('grounding_bar_terkoneksi', 100)->nullable();
            $table->string('ac_pendingin_ruangan', 100)->nullable();
            $table->integer('suhu_ruangan_perangkat')->nullable();
            $table->text('modem_quality_data')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('form_checklist_wireline');
    }
};
