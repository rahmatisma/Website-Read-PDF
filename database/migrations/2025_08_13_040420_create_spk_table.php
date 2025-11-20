<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: SPK (Surat Perintah Kerja)
     * Purpose: Work order utama
     * Depends on: jaringan
     */
    public function up(): void
    {
        Schema::create('spk', function (Blueprint $table) {
            // Primary Key
            $table->id('id_spk');

            // SPK Information
            $table->string('no_spk', 100)->unique();
            $table->string('no_jaringan', 100);

            // Document & Work Order Type
            $table->enum('document_type', [
                'spk',
                'form_checklist_wireline',
                'form_checklist_wireless'
            ]);

            $table->enum('jenis_spk', [
                'aktivasi',
                'dismantle',
                'instalasi',
                'survey',
                'maintenance'
            ]);

            // SPK Details
            $table->date('tanggal_spk');
            $table->string('no_mr', 100)->nullable();
            $table->string('no_fps', 100)->nullable();

            // Soft Delete Fields
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->text('deletion_reason')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('no_jaringan');
            $table->index('jenis_spk');
            $table->index('tanggal_spk');
            $table->index('is_deleted');
            $table->index(['is_deleted', 'jenis_spk', 'tanggal_spk']);

            // Foreign Key
            $table->foreign('no_jaringan')
                ->references('no_jaringan')
                ->on('jaringan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk');
    }
};
