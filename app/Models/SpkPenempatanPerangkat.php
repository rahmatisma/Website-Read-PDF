<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkPenempatanPerangkat extends Model
{
    protected $table = 'spk_penempatan_perangkat';
    protected $primaryKey = 'id_penempatan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_penempatan_modem_dan_router',
        'kesiapan_ruang_server',
        'ketersedian_rak_server',
        'space_modem_dan_router',
        'diizinkan_foto_ruang_server_pelanggan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}