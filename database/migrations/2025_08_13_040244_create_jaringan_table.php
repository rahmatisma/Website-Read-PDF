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
        Schema::create('jaringan', function (Blueprint $table) {
            $table->id('id_jaringan');
            $table->string('no_jaringan', 20)->index();
            $table->foreignId('id_pelanggan')->constrained('pelanggan','id_pelanggan')->cascadeOnDelete();
            $table->string('jenis_layanan', 50);
            $table->string('media_akses', 50);
            $table->string('kecepatan', 100);
            $table->date('tgl_rfs_la')->nullable();
            $table->date('tgl_rfs_plg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaringan');
    }
};
