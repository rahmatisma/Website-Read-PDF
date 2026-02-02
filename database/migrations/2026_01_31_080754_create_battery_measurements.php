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
        Schema::create('battery_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battery_bank_id')->constrained('battery_bank_metadata')->onDelete('cascade');
            $table->integer('cell_number')->comment('1-20');
            $table->decimal('voltage', 5, 2)->nullable()->comment('Voltage reading');
            $table->integer('soh')->nullable()->comment('State of Health percentage');
            $table->timestamps();
            
            $table->unique(['battery_bank_id', 'cell_number']);
            $table->index('battery_bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_measurements');
    }
};