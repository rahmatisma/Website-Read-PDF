<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkExecutionInfo extends Model
{
    protected $table = 'spk_execution_info';
    protected $primaryKey = 'id_execution';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'latitude',
        'longitude',
        'pic_pelanggan',
        'kontak_pic_pelanggan',
        'teknisi',
        'nama_vendor',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}