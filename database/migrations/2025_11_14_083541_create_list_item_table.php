<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: List_Item
     * Purpose: Daftar item/material yang digunakan (multiple items per SPK)
     * Depends on: spk
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('list_item', function (Blueprint $table) {
            // Primary Key
            $table->id('id_item');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_spk');

            // Item Information
            $table->string('kode', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('serial_number', 100)->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

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
        Schema::dropIfExists('list_item');
    }
};
