<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    protected $table = 'berita_acara';
    protected $primaryKey = 'id_berita_acara';

    protected $fillable = [
        'id_spk',
        'judul_spk',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}