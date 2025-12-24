<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcwl_outdoor_parameter', function (Blueprint $table) {
            $table->id('id_parameter');
            $table->unsignedBigInteger('id_outdoor');
            $table->enum('kategori', ['site', 'sarana_penunjang', 'perangkat_antenna', 'cabling_installation']);
            $table->string('parameter', 255);
            $table->text('standard')->nullable();
            $table->text('existing')->nullable();
            $table->integer('urutan')->nullable();

            // Indexes
            $table->index('id_outdoor');
            $table->index(['id_outdoor', 'kategori']);

            // Foreign key
            $table->foreign('id_outdoor')
                ->references('id_outdoor')
                ->on('fcwl_outdoor_area')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcwl_outdoor_parameter');
    }
};
