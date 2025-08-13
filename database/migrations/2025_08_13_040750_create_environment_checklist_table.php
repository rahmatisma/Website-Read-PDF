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
        Schema::create('environment_checklist', function (Blueprint $table) {
            $table->id('id_environment');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->boolean('ac_pendingin')->default(false);
            $table->decimal('suhu_ruangan', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('environment_checklist');
    }
};
