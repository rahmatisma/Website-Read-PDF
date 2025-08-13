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
        Schema::create('item_pekerjaan', function (Blueprint $table) {
            $table->id('id_item');
            $table->foreignId('id_spk')->constrained('spk','id_spk')->cascadeOnDelete();
            $table->foreignId('id_perangkat')->constrained('perangkat','id_perangkat')->cascadeOnDelete();
            $table->string('kategori', 50); // EXISTING, CABUT, TIDAK TERPAKAI, PENGGANTI/PASANG BARU
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pekerjaan');
    }
};
