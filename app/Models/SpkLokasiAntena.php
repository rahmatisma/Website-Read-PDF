<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkLokasiAntena extends Model
{
    protected $table = 'spk_lokasi_antena';
    protected $primaryKey = 'id_lokasi_antena';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_antena',
        'detail_lokasi_antena',
        'space_tersedia',
        'akses_di_lokasi_perlu_alat_bantu',
        'penangkal_petir',
        'tinggi_penangkal_petir',
        'jarak_ke_lokasi_antena',
        'tindak_lanjut',
        'tower_pole',
        'pemilik_tower_pole',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}