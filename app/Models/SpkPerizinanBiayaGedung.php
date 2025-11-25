<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkPerizinanBiayaGedung extends Model
{
    protected $table = 'spk_perizinan_biaya_gedung';
    protected $primaryKey = 'id_perizinan_gedung';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'pic_bm',
        'kontak_pic_bm',
        'material_dan_infrastruktur',
        'panjang_kabel_dalam_gedung',
        'pelaksana_penarikan_kabel_dalam_gedung',
        'waktu_pelaksanaan_penarikan_kabel',
        'supervisi',
        'deposit_kerja',
        'ikg_instalasi_kabel_gedung',
        'biaya_sewa',
        'biaya_lain',
        'info_lain_lain_jika_ada',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}