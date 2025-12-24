<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcw_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_fcw');
            $table->timestamp('date_time');
            $table->text('info');
            $table->string('photo', 1000)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcw');
            $table->index('date_time');

            // Foreign Key
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcw_log');
    }
};