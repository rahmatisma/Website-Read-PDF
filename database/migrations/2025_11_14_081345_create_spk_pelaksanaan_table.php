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
        Schema::create('spk_pelaksanaan', function (Blueprint $table) {
            $table->id('id_pelaksanaan');
            $table->unsignedBigInteger('id_spk')->unique();
            $table->timestamp('permintaan_pelanggan')->nullable();
            $table->timestamp('datang')->nullable();
            $table->timestamp('selesai')->nullable();

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
        Schema::dropIfExists('spk_pelaksanaan');
    }
};
