<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: Form_Checklist_Wireless
     * Purpose: Form checklist untuk aktivasi wireless
     * Depends on: spk
     */
    public function up(): void
    {
        Schema::create('form_checklist_wireless', function (Blueprint $table) {
            // Primary Key
            $table->id('id_fcwl');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_spk')->unique();

            // Form Information
            $table->string('no_spk', 100);
            $table->date('tanggal');

            // Location
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();

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
        Schema::dropIfExists('form_checklist_wireless');
    }
};
