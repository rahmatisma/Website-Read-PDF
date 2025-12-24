<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_checklist_wireless', function (Blueprint $table) {
            $table->id('id_fcwl');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->string('no_spk', 100);
            $table->date('tanggal');

            // Data Remote
            $table->string('nama_pelanggan', 255)->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();

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
        Schema::dropIfExists('form_checklist_wireless');
    }
};
