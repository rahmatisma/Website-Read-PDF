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
        Schema::create('line_checklist', function (Blueprint $table) {
            $table->id('id_checklist');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->string('checkpoint', 100);
            $table->string('line_checklist', 100);
            $table->text('standard');
            $table->text('existing')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('hasil_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_checklist');
    }
};
