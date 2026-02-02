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
        Schema::create('equipment_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('form_code', 50)->default('FM-LAP-D2-SOP-003-012');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->onDelete('set null')->comment('Link to main inspection if part of one');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->date('inventory_date');
            $table->time('inventory_time')->nullable();
            
            // Approval
            $table->foreignId('pelaksana1_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana2_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana3_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana4_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('verified_by_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['location_id', 'inventory_date'], 'idx_inventory_loc_date');
            $table->index('inspection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_inventory');
    }
};