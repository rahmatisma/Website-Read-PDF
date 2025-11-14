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
        Schema::create('fcw_guidance_foto', function (Blueprint $table) {
            $table->id('id_guidance');
            $table->unsignedBigInteger('id_fcw');
            $table->string('jenis_foto', 255)->nullable();
            $table->text('patch_foto')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade');

            // Index
            $table->index('id_fcw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_guidance_foto');
    }
};
