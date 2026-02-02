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
        Schema::create('schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('maintenance_schedules')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->integer('row_number')->comment('1-11');
            $table->string('assigned_officer', 100)->nullable();
            $table->text('notes')->nullable();
            $table->integer('day_of_month')->comment('1-31');
            $table->string('plan_type', 20)->comment('RENCANA or REALISASI');
            $table->boolean('is_checked')->default(false);
            $table->timestamps();
            
            $table->unique(
                ['schedule_id', 'row_number', 'day_of_month', 'plan_type'],
                'uq_sched_det_sched_row_day_plan'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_details');
    }
};