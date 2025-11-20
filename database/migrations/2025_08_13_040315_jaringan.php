<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table: JARINGAN
     * Purpose: Master data pelanggan dan jaringan
     */
    public function up(): void
    {
        Schema::create('jaringan', function (Blueprint $table) {
            // Primary Key
            $table->string('no_jaringan', 100)->primary();
            
            // Customer Information
            $table->string('nama_pelanggan', 255);
            $table->text('lokasi_pelanggan');
            $table->string('jasa', 100);
            
            // Network Specifications
            $table->string('media_akses', 100)->nullable();
            $table->string('kecepatan', 255)->nullable();
            $table->string('manage_router', 255)->nullable();
            $table->string('opsi_router', 255)->nullable();
            $table->string('ip_lan', 100)->nullable();
            
            // Network Identifiers
            $table->string('kode_jaringan', 100)->nullable();
            $table->string('no_fmb', 100)->nullable();
            $table->text('pop')->nullable();
            
            // RFS Dates
            $table->date('tgl_rfs_la')->nullable();
            $table->date('tgl_rfs_plg')->nullable();
            
            // Soft Delete Fields
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->text('deletion_reason')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('nama_pelanggan');
            $table->index('jasa');
            $table->index('is_deleted');
            $table->index(['is_deleted', 'jasa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaringan');
    }
};