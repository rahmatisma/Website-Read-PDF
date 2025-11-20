<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK_Pelaksanaan
     * Purpose: Tracking waktu pelaksanaan SPK
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('spk_pelaksanaan', function (Blueprint $table) {
            // Primary Key
            $table->id('id_pelaksanaan');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Execution Timeline
            $table->timestamp('permintaan_pelanggan');
            $table->timestamp('datang')->nullable();
            $table->timestamp('selesai')->nullable();

            // Indexes
            $table->index('id_spk');
            $table->index('permintaan_pelanggan');

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
        Schema::dropIfExists('spk_pelaksanaan');
    }
};
