<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    protected $table = 'spk';
    protected $primaryKey = 'id_spk';

    protected $fillable = [
        'no_spk',
        'no_jaringan',
        'document_type',
        'jenis_spk',
        'tanggal_spk',
        'no_mr',
        'no_fps',
    ];

    protected $casts = [
        'tanggal_spk' => 'date',
    ];

    public function jaringan()
    {
        return $this->belongsTo(Jaringan::class, 'no_jaringan', 'no_jaringan');
    }

    public function pelaksanaan()
    {
        return $this->hasOne(SpkPelaksanaan::class, 'id_spk');
    }

    public function executionInfo()
    {
        return $this->hasOne(SpkExecutionInfo::class, 'id_spk');
    }

    public function informasiGedung()
    {
        return $this->hasOne(SpkInformasiGedung::class, 'id_spk');
    }

    public function lokasiAntena()
    {
        return $this->hasOne(SpkLokasiAntena::class, 'id_spk');
    }

    public function perizinanBiayaGedung()
    {
        return $this->hasOne(SpkPerizinanBiayaGedung::class, 'id_spk');
    }

    public function penempatanPerangkat()
    {
        return $this->hasOne(SpkPenempatanPerangkat::class, 'id_spk');
    }

    public function perizinanBiayaKawasan()
    {
        return $this->hasOne(SpkPerizinanBiayaKawasan::class, 'id_spk');
    }

    public function kawasanUmum()
    {
        return $this->hasOne(SpkKawasanUmum::class, 'id_spk');
    }

    public function dataSplitter()
    {
        return $this->hasOne(SpkDataSplitter::class, 'id_spk');
    }

    public function hhEksisting()
    {
        return $this->hasMany(SpkHhEksisting::class, 'id_spk');
    }

    public function hhBaru()
    {
        return $this->hasMany(SpkHhBaru::class, 'id_spk');
    }

    public function dokumentasiFoto()
    {
        return $this->hasMany(DokumentasiFoto::class, 'id_spk');
    }

    public function beritaAcara()
    {
        return $this->hasOne(BeritaAcara::class, 'id_spk');
    }
}