<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkKawasanUmum extends Model
{
    protected $table = 'spk_kawasan_umum';
    protected $primaryKey = 'id_kawasan_umum';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nama_kawasan_umum_pu_yang_dilewati',
        'panjang_jalur_outdoor_di_kawasan_umum',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}