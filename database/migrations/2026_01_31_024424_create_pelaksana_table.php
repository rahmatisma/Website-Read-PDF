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
        Schema::create('pelaksana', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('department', 100)->nullable();
            $table->string('sub_department', 100)->nullable();
            $table->string('perusahaan', 100)->nullable()->comment('Untuk form yang pakai kolom Perusahaan');
            $table->string('nik', 50)->nullable()->unique();
            $table->string('mitra_internal', 20)->nullable()->comment('MITRA atau INTERNAL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksana');
    }
};