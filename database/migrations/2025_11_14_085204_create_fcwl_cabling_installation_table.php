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
        Schema::create('fcwl_cabling_installation', function (Blueprint $table) {
            $table->id('id_cabling');
            $table->unsignedBigInteger('id_fcwl')->unique();
            $table->string('type_kabel_ifl', 100)->nullable();
            $table->string('panjang_kabel_ifl', 100)->nullable();
            $table->string('tahanan_short_kabel_ifl', 100)->nullable();
            $table->string('terpasang_arrestor', 100)->nullable();
            $table->string('splicing_konektor_kabel_ifl', 100)->nullable();

            // Foreign key constraint
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade');

            // Index
            $table->index('id_fcwl');
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
