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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_code', 50)->unique()->comment('Auto: INS-20250130-001');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->date('inspection_date');
            $table->time('inspection_time')->nullable();
            $table->string('status', 20)->default('DRAFT')->comment('DRAFT, COMPLETED, APPROVED');
            $table->text('notes')->nullable();
            
            // Executors - 4 slots
            $table->foreignId('pelaksana1_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana2_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana3_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana4_id')->nullable()->constrained('pelaksana')->onDelete('set null')->comment('4th executor slot');
            
            // Approval - 2 levels
            $table->foreignId('verified_by_id')->nullable()->constrained('pelaksana')->onDelete('set null')->comment('Verifikator (1st approval)');
            $table->foreignId('approved_by_id')->nullable()->constrained('pelaksana')->onDelete('set null')->comment('Head Of Sub Department (2nd approval)');
            
            $table->timestamps();
            
            $table->index(['inspection_date', 'location_id'], 'idx_inspection_date_loc');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};