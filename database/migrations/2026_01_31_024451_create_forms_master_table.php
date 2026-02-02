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
        Schema::create('forms_master', function (Blueprint $table) {
            $table->id();
            $table->string('form_code', 50)->unique()->comment('FM-LAP-D2-SOP-003-004, 005, dll');
            $table->string('form_title', 200);
            $table->string('form_type', 50)->nullable()->comment('INSPECTION, FOLLOWUP, SCHEDULE, INVENTORY');
            $table->foreignId('equipment_type_id')->nullable()->constrained('equipment_types')->onDelete('set null')->comment('NULL untuk form general');
            $table->string('version', 10)->default('1.0');
            $table->integer('page_total')->default(1)->comment('Battery = 2 pages');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('equipment_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms_master');
    }
};