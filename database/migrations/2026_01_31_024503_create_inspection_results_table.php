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
        Schema::create('inspection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_form_id')->constrained('inspection_forms')->onDelete('cascade');
            $table->foreignId('checklist_item_id')->constrained('forms_checklist_master')->onDelete('restrict');
            
            // Result data
            $table->text('result_value')->nullable()->comment('Actual measurement or observation');
            $table->string('status', 10)->nullable()->comment('OK, NOK');
            $table->text('comment')->nullable()->comment('Additional notes for this specific item');
            
            // For multiple measurements (R-S-T, temps at different points)
            $table->string('measurement_label', 50)->nullable()->comment('R-S, S-T, T-R, Input UPS, Output UPS, etc');
            
            // For grid-style data (Battery form)
            $table->integer('row_number')->nullable()->comment('For battery grid: 1-20');
            $table->string('column_name', 50)->nullable()->comment('For battery grid: Voltage, SOH');
            
            $table->timestamps();
            
            $table->index(['inspection_form_id', 'checklist_item_id'], 'idx_result_form_item');
            $table->index(['inspection_form_id', 'row_number', 'column_name'], 'idx_result_grid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_results');
    }
};