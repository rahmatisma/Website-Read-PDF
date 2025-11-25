<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkHhEksisting extends Model
{
    protected $table = 'spk_hh_eksisting';
    protected $primaryKey = 'id_hh_eksisting';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nomor_hh',
        'kondisi_hh',
        'lokasi_hh',
        'latitude',
        'longitude',
        'ketersediaan_closure',
        'kapasitas_closure',
        'kondisi_closure',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}