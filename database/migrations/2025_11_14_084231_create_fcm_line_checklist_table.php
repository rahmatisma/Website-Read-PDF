<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCW_Line_Checklist
     * Purpose: Checklist poin pemeriksaan line (multiple checkpoints)
     * Depends on: form_checklist_wireline
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('fcw_line_checklist', function (Blueprint $table) {
            // Primary Key
            $table->id('id_line_check');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcw');

            // Checkpoint Information
            $table->string('check_point', 255);

            // Checklist Stages
            $table->text('standard')->nullable();
            $table->text('existing')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('hasil_akhir')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index('id_fcw');

            // Foreign Key Constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_line_checklist');
    }
};
