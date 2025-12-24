<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcw_checklist_item', function (Blueprint $table) {
            $table->id('id_checklist');
            $table->unsignedBigInteger('id_fcw');
            $table->enum('kategori', [
                'indikator_modem',
                'merek',
                'modem_fo',
                'lc_signal_kop',
                'lc_signal_avo',
                'site_area',
                'hrb_r_lintas',
                'line_fo',
                'tes_konektivitas'
            ]);
            $table->string('check_point', 255);
            $table->text('standard')->nullable();
            $table->text('nms_engineer')->nullable();
            $table->text('on_site_teknisi')->nullable();
            $table->text('existing')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('hasil_akhir')->nullable();
            $table->integer('urutan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('id_fcw');
            $table->index(['id_fcw', 'kategori']);
            $table->index('kategori');

            // Foreign Key
            $table->foreign('id_fcw')
                ->references('id_fcw')
                ->on('form_checklist_wireline')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcw_checklist_item');
    }
};