<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumentasiFoto extends Model
{
    protected $table = 'dokumentasi_foto';
    protected $primaryKey = 'id_dokumentasi';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'kategori_foto',
        'path_foto',
        'urutan',
        'keterangan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}