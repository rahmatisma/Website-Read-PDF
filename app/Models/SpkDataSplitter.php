<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkDataSplitter extends Model
{
    protected $table = 'spk_data_splitter';
    protected $primaryKey = 'id_splitter';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_splitter',
        'id_splitter_text',
        'kapasitas_splitter',
        'jumlah_port_kosong',
        'list_port_kosong_dan_redaman',
        'nama_node_jika_tidak_ada_splitter',
        'list_port_kosong',
        'arah_akses',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}