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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id('id_upload'); // Primary key
            $table->string('source_type', 20); // user atau system
            $table->unsignedBigInteger('id_user')->nullable(); // FK ke users
            $table->string('source_system', 100)->nullable(); // nama/kode system pengirim
            $table->string('document_type', 50); // pemasangan, pencabutan, dll
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('file_type', 50); // pdf, doc, jpg, dll
            $table->bigInteger('file_size')->nullable(); // dalam byte
            $table->enum('status', ['uploaded', 'processing', 'completed', 'failed']);
            $table->json('extracted_data')->nullable(); // ⬅️ TAMBAH INI
            $table->timestamps();

            // Foreign key
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
