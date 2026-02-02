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
        Schema::create('followup_actions', function (Blueprint $table) {
            $table->id();
            $table->string('form_code', 50)->default('FM-LAP-D2-SOP-003-005');
            $table->foreignId('followup_request_id')->nullable()->constrained('followup_requests')->onDelete('set null');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->onDelete('set null');
            $table->string('based_on', 50)->nullable()->comment('FOLLOWUP_REQUEST or PM_EXECUTION');
            $table->date('action_date');
            $table->time('action_time')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('room', 100)->nullable();
            $table->text('problem_description');
            $table->text('resolution_action');
            $table->text('repair_result');
            $table->string('completion_status', 20)->nullable()->comment('COMPLETED, NOT_COMPLETED');
            $table->date('completion_date')->nullable();
            $table->time('completion_time')->nullable();
            $table->text('followup_note')->nullable();
            $table->foreignId('pelaksana1_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana2_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana3_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('pelaksana4_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->foreignId('approved_by_id')->nullable()->constrained('pelaksana')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_actions');
    }
};