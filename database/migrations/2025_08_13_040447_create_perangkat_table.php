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
        Schema::create('perangkat', function (Blueprint $table) {
            $table->id('id_perangkat');
            $table->string('nama_perangkat', 255);
            $table->string('no_reg', 50)->nullable()->index();
            $table->string('serial_number', 50)->nullable()->index();
            $table->text('spesifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkat');
    }
};
