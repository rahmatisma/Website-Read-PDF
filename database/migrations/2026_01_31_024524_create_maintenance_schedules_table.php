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
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('form_code', 50)->default('FM-LAP-D2-SOP-003-007');
            $table->integer('month')->comment('1-12');
            $table->integer('year');
            $table->foreignId('created_by_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->string('created_by_nik', 50)->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->string('approved_by_nik', 50)->nullable();
            $table->timestamps();
            
            $table->unique(['month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};