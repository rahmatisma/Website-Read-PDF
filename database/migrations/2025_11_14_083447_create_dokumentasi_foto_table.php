<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: Dokumentasi_Foto
     * Purpose: Dokumentasi foto untuk berbagai kategori (multiple photos per SPK)
     * Depends on: spk
     * Relationship: ONE-TO-MANY
     */
    public function up(): void
    {
        Schema::create('dokumentasi_foto', function (Blueprint $table) {
            // Primary Key
            $table->id('id_dokumentasi');

            // Foreign Key (NOT unique - one-to-many relationship)
            $table->unsignedBigInteger('id_spk');

            // Photo Category (14 categories)
            $table->enum('kategori_foto', [
                'hasil_survey',
                'hasil_instalasi',
                'hasil_aktivasi',
                'hasil_dismantle',
                'foto_penempatan_perangkat',
                'foto_jalur_kabel',
                'plan_jalur_gedung',
                'data_jalur_kabel',
                'foto_splitter',
                'foto_hh_eksisting',
                'foto_hh_baru',
                'foto_dokumentasi_umum',
                'foto_lain_lain',
                'list_item'
            ]);

            // Photo Information
            $table->string('path_foto', 1000);
            $table->integer('urutan')->nullable();
            $table->text('keterangan')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_spk');
            $table->index(['id_spk', 'kategori_foto', 'urutan']);
            $table->index('kategori_foto');
            $table->index('created_at');

            // Foreign Key Constraint
            $table->foreign('id_spk')
                ->references('id_spk')
                ->on('spk')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_foto');
    }
};
