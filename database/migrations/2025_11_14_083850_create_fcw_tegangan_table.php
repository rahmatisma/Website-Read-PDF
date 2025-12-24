<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcw_tegangan', function (Blueprint $table) {
            $table->id('id_tegangan');
            $table->unsignedBigInteger('id_fcw');
            $table->enum('jenis_sumber', ['pln', 'ups', 'it', 'generator']);
            $table->decimal('p_n', 6, 2)->nullable();
            $table->decimal('p_g', 6, 2)->nullable();
            $table->decimal('n_g', 6, 2)->nullable();

            // Indexes
            $table->index('id_fcw');
            $table->unique(['id_fcw', 'jenis_sumber']);

            // Foreign Key
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcw_tegangan');
    }
};