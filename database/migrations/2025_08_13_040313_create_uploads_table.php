<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id('id_upload');
            $table->string('source_type', 20);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('source_system', 100)->nullable();
            $table->string('document_type', 50);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('file_type', 50);
            $table->bigInteger('file_size')->nullable();
            $table->enum('status', ['uploaded', 'processing', 'completed', 'failed'])->default('uploaded');
            $table->json('extracted_data')->nullable();
            $table->timestamps();

            // Foreign key ke users
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
