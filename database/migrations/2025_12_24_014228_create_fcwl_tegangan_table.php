<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcwl_tegangan', function (Blueprint $table) {
            $table->id('id_tegangan');
            $table->unsignedBigInteger('id_fcwl');
            $table->enum('jenis_sumber', ['pln', 'ups', 'it', 'generator']);
            $table->decimal('p_n', 6, 2)->nullable();
            $table->decimal('p_g', 6, 2)->nullable();
            $table->decimal('n_g', 6, 2)->nullable();

            // Indexes
            $table->index('id_fcwl');
            $table->unique(['id_fcwl', 'jenis_sumber']);

            // Foreign key
            $table->foreign('id_fcwl')
                ->references('id_fcwl')
                ->on('form_checklist_wireless')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcwl_tegangan');
    }
};
