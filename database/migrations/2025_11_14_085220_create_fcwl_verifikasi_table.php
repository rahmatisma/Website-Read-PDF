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
        Schema::create('fcwl_verifikasi', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->unsignedBigInteger('id_fcwl');
            $table->enum('role', ['pelaksana', 'pelanggan', 'verifikator']);
            $table->string('nama', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->text('ttd')->nullable();

            // Foreign key constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_fcwl');
            $table->unique(['id_fcwl', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_verifikasi');
    }
};
