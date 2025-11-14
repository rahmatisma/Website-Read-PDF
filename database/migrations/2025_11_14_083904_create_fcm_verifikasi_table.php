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
        Schema::create('fcw_verifikasi', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->unsignedBigInteger('id_fcw');
            $table->enum('role', ['pelaksana', 'pelanggan', 'verifikator']);
            $table->string('nama', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->text('ttd')->nullable();

            // Foreign key constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_verifikasi');
    }
};
