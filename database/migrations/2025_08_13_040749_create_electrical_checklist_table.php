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
        Schema::create('electrical_checklist', function (Blueprint $table) {
            $table->id('id_electrical');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->decimal('p_n_pln', 5, 1)->nullable();
            $table->decimal('p_n_ups', 5, 1)->nullable();
            $table->decimal('p_n_it', 5, 1)->nullable();
            $table->decimal('p_g_pln', 5, 1)->nullable();
            $table->decimal('p_g_ups', 5, 1)->nullable();
            $table->decimal('p_g_it', 5, 1)->nullable();
            $table->decimal('n_g_pln', 5, 1)->nullable();
            $table->decimal('n_g_ups', 5, 1)->nullable();
            $table->decimal('n_g_it', 5, 1)->nullable();
            $table->boolean('grounding_bar_terkoneksi')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electrical_checklist');
    }
};
