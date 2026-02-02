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
        Schema::create('inspection_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->onDelete('cascade');
            $table->foreignId('form_id')->constrained('forms_master')->onDelete('restrict');
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->onDelete('set null')->comment('NULL untuk form general seperti Inventory');
            $table->integer('instance_number')->default(1)->comment('If same form used multiple times in one inspection');
            $table->string('status', 20)->default('DRAFT');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['inspection_id', 'form_id', 'instance_number']);
            $table->index('equipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_forms');
    }
};