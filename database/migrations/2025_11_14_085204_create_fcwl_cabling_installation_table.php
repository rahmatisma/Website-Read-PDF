<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCWL_Cabling_Installation
     * Purpose: Data instalasi kabel IFL untuk wireless
     * Depends on: form_checklist_wireless
     */
    public function up(): void
    {
        Schema::create('fcwl_cabling_installation', function (Blueprint $table) {
            // Primary Key
            $table->id('id_cabling');

            // Foreign Key (unique - one-to-one relationship)
            $table->unsignedBigInteger('id_fcwl')->unique();

            // IFL Cable Information
            $table->string('type_kabel_ifl', 100)->nullable();
            $table->string('panjang_kabel_ifl', 100)->nullable();
            $table->string('tahanan_short_kabel_ifl', 100)->nullable();

            // Arrestor
            $table->enum('terpasang_arrestor', ['ya', 'tidak'])->nullable();

            // Cable Connection
            $table->string('splicing_konektor_kabel_ifl', 100)->nullable();

            // Index
            $table->index('id_fcwl');

            // Foreign Key Constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcwl_cabling_installation');
    }
};
