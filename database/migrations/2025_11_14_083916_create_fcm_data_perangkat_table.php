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
        Schema::create('fcw_data_perangkat', function (Blueprint $table) {
            $table->id('id_perangkat');
            $table->unsignedBigInteger('id_fcw');
            $table->enum('kategori', [
                'existing',
                'tidak_terpakai',
                'cabut',
                'pengganti_pasang_baru'
            ])->nullable();
            $table->string('nama_barang', 255)->nullable();
            $table->string('no_reg', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraint
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade');

            // Indexes
            $table->index('id_fcw');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcw_data_perangkat');
    }
};
