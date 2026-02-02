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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('equipment_inventory')->onDelete('cascade');
            $table->integer('row_number');
            $table->string('section_name', 100)->nullable()->comment('I. DEVICE SENTRAL, II. SUPPORTING FACILITIES');
            $table->string('equipment_name', 200);
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->onDelete('set null')->comment('Link to equipment master if exists');
            $table->integer('quantity');
            $table->string('status', 20)->comment('ACTIVE, SHUTDOWN');
            $table->string('bonding_ground', 20)->nullable()->comment('CONNECT, NOT CONNECT');
            $table->text('remarks')->nullable()->comment('KETERANGAN');
            $table->timestamps();
            
            $table->unique(['inventory_id', 'row_number']);
            $table->index('inventory_id');
            $table->index('equipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};