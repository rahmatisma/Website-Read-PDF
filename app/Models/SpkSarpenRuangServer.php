<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkSarpenRuangServer extends Model
{
    protected $table = 'spk_sarpen_ruang_server';
    protected $primaryKey = 'id_sarpen';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'power_line_listrik',
        'ketersediaan_power_outlet',
        'grounding_listrik',
        'ups',
        'ruangan_ber_ac',
        'suhu_ruangan_value',
        'suhu_ruangan_keterangan',
        'lantai',
        'ruang',
        'perangkat_pelanggan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}