<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkPerizinanBiayaKawasan extends Model
{
    protected $table = 'spk_perizinan_biaya_kawasan';
    protected $primaryKey = 'id_perizinan_kawasan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'melewati_kawasan_private',
        'nama_kawasan',
        'pic_kawasan',
        'kontak_pic_kawasan',
        'panjang_kabel_dalam_kawasan',
        'pelaksana_penarikan_kabel_dalam_kawasan',
        'deposit_kerja',
        'supervisi',
        'biaya_penarikan_kabel_dalam_kawasan',
        'biaya_sewa',
        'biaya_lain',
        'info_lain_lain_jika_ada',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}