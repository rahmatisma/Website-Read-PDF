<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: Form_Checklist_Wireline
     * Purpose: Form checklist untuk aktivasi wireline
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('form_checklist_wireline', function (Blueprint $table) {
            // Primary Key
            $table->id('id_fcw');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Form Information
            $table->string('no_spk', 100);
            $table->date('tanggal');

            // Location
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            // Room Information
            $table->string('posisi_modem_di_lt', 100)->nullable();
            $table->string('ruang', 100)->nullable();

            // Room Conditions
            $table->enum('grounding_bar_terkoneksi', ['ya', 'tidak'])->nullable();
            $table->enum('ac_pendingin_ruangan', ['ada', 'tidak_ada'])->nullable();
            $table->decimal('suhu_ruangan_perangkat', 4, 1)->nullable();

            // Modem Quality Data
            $table->text('modem_quality_data')->nullable();

            // Timestamps
            $table->timestamps();

            // Index
            $table->index('id_spk');

            // Foreign Key Constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
