<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkPelaksanaan extends Model
{
    protected $table = 'spk_pelaksanaan';
    protected $primaryKey = 'id_pelaksanaan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'permintaan_pelanggan',
        'datang',
        'selesai',
    ];

    protected $casts = [
        'permintaan_pelanggan' => 'datetime',
        'datang' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}