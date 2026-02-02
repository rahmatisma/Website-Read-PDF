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
        Schema::create('followup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('form_code', 50)->default('FM-LAP-D2-SOP-003-004');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->onDelete('set null')->comment('NULL jika bukan dari inspection');
            $table->date('request_date');
            $table->time('request_time')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('room', 100)->nullable();
            $table->text('problem_description');
            $table->text('proposed_action');
            $table->string('requester_name', 100);
            $table->string('requester_department', 100)->nullable();
            $table->string('requester_sub_department', 100)->nullable();
            $table->string('addressed_to_department', 100)->default('Operations & Maintenance Support');
            $table->string('addressed_to_sub_department', 100)->nullable();
            $table->string('delivery_method', 20)->nullable()->comment('Email, Fax, Hardcopy');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('PENDING')->comment('PENDING, IN_PROGRESS, COMPLETED');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_requests');
    }
};