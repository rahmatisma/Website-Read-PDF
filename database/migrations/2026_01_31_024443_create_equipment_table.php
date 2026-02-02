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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_type_id')->constrained('equipment_types')->onDelete('restrict');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('brand', 100)->nullable();
            $table->string('model_type', 100)->nullable();
            $table->string('capacity', 50)->nullable()->comment('AC: 1 PK, 2 PK; Inverter: 500VA, 1000VA, 2000VA');
            $table->string('reg_number', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            
            // Equipment-specific fields
            $table->string('kap_power_module', 50)->nullable()->comment('Rectifier: Single/Dual/Three');
            $table->string('type_pole', 100)->nullable()->comment('Pole/Tower: SST/Pole/Tripole/Triangle/Triangle Wired');
            $table->string('height', 50)->nullable()->comment('Pole/Tower height');
            
            // Flexible metadata for additional equipment-specific data
            $table->json('metadata')->nullable()->comment('Flexible storage for equipment-specific attributes');
            
            $table->string('status', 20)->default('ACTIVE')->comment('ACTIVE, SHUTDOWN, MAINTENANCE');
            $table->timestamps();
            
            // Indexes
            $table->index(['equipment_type_id', 'location_id'], 'idx_equipment_type_loc');
            $table->index('reg_number');
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};