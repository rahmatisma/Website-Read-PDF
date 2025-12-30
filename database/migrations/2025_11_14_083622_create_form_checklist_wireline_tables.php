<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_checklist_wireline', function (Blueprint $table) {
            $table->id('id_fcw');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('no_spk', 100);
            $table->date('tanggal');

            // Data Remote
            $table->string('nama_pelanggan', 255)->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->text('alamat')->nullable();

            // Global Checklist - Data Lokasi
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('posisi_modem_di_lt', 100)->nullable();
            $table->string('ruang', 100)->nullable();

            // Global Checklist - Environment
            $table->string('grounding_bar_terkoneksi_ke', 255)->nullable();
            $table->enum('ac_pendingin_ruangan', ['ada', 'tidak_ada'])->nullable();
            $table->decimal('suhu_ruangan_perangkat', 4, 1)->nullable();

            $table->timestamps();

            // Indexes
            $table->index('id_spk');
            $table->index('no_spk');

            // Foreign Key
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_checklist_wireline');
    }
};