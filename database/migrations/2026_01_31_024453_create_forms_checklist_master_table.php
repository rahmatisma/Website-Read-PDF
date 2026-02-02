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
        Schema::create('forms_checklist_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms_master')->onDelete('cascade');
            
            // Section hierarchy - supports nested sections
            $table->foreignId('parent_section_id')->nullable()->constrained('forms_checklist_master')->onDelete('cascade')->comment('NULL for root sections, enables nesting like 2.I.a');
            $table->integer('section_number')->comment('1, 2, 3 for main sections');
            $table->string('section_name', 200)->nullable()->comment('Physical Check, Performance Check, etc');
            
            // Item identification
            $table->string('item_code', 20)->nullable()->comment('a, b, c, I, II, I.a, II.b');
            $table->text('item_description')->comment('Environment Condition, AC input voltage, dll');
            $table->text('operational_standard')->comment('Expected value or range');
            
            // Ordering and display
            $table->integer('item_order')->comment('Display sequence within parent');
            
            // Measurement configuration
            $table->boolean('has_multiple_measurements')->default(false)->comment('True untuk R-S-T, MCB temps, dll');
            $table->string('measurement_labels', 200)->nullable()->comment('Comma-separated: R-S,S-T,T-R or Input,Output,Bypass');
            
            // Conditional measurements (based on equipment properties)
            $table->boolean('is_conditional')->default(false)->comment('True if standard varies by equipment config');
            $table->string('conditional_field', 100)->nullable()->comment('Field name to check: kap_power_module, capacity, etc');
            $table->json('conditional_standards')->nullable()->comment('Map of field_value -> operational_standard');
            
            $table->timestamps();
            
            $table->index(['form_id', 'section_number', 'item_order'], 'idx_checklist_order');
            $table->index('parent_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms_checklist_master');
    }
};