<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcwl_indoor_parameter', function (Blueprint $table) {
            $table->id('id_parameter');
            $table->unsignedBigInteger('id_indoor');
            $table->enum('kategori', ['sarana_penunjang', 'perangkat_modem', 'perangkat_cpe']);
            $table->string('quality_parameter', 255);
            $table->text('standard')->nullable();
            $table->text('existing')->nullable();
            $table->integer('urutan')->nullable();

            // Indexes
            $table->index('id_indoor');
            $table->index(['id_indoor', 'kategori']);

            // Foreign key
            $table->foreign('id_indoor')
                ->references('id_indoor')
                ->on('fcwl_indoor_area')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcwl_indoor_parameter');
    }
};
