<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkHhBaru extends Model
{
    protected $table = 'spk_hh_baru';
    protected $primaryKey = 'id_hh_baru';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nomor_hh',
        'lokasi_hh',
        'latitude',
        'longitude',
        'kebutuhan_penambahan_closure',
        'kapasitas_closure',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}