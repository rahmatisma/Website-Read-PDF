<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: FCW_Log
     * Purpose: Activity log untuk form wireline
     * Depends on: form_checklist_wireline
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('fcw_log', function (Blueprint $table) {
            // Primary Key
            $table->id('id_log');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_fcw');

            // Log Information
            $table->timestamp('date_time');
            $table->text('info');
            $table->string('photo', 1000)->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcw');
            $table->index('date_time');

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
        Schema::dropIfExists('fcw_log');
    }
};
