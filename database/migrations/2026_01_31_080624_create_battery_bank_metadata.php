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
        Schema::create('battery_bank_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_form_id')
                ->constrained('inspection_forms')
                ->onDelete('cascade')
                ->comment('Links to Battery inspection');
            
            $table->integer('bank_number')->comment('1, 2, 3, etc');
            $table->string('bank_name', 100)->comment('Bank 1 UPS, Bank 1 Recti, Bank 2 UPS, etc');
            $table->string('battery_type', 100)->nullable()->comment('Baterai Kering UPS, Baterai Kering Recti');
            $table->string('battery_brand', 100)->nullable()->comment('Ritar, Yuasa, etc');
            $table->string('battery_capacity', 50)->nullable()->comment('65 AH, 80 AH, etc (End Device Batt)');
            $table->date('production_date')->nullable()->comment('Tanggal Produksi (extracted from notes or manual input)');
            $table->text('notes')->nullable()->comment('General notes about battery bank (e.g., "Baterai Normal", production dates, age info)');
            $table->timestamps();
            
            // Karena bisa ada: Bank 1 UPS dan Bank 1 Recti di inspection yang sama
            $table->unique(['inspection_form_id', 'bank_name'], 'battery_bank_unique');
            
            // Index untuk query performance
            $table->index('inspection_form_id');
            $table->index(['inspection_form_id', 'bank_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_bank_metadata');
    }
};