<?php

namespace App\Models;

use Dom\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SPK extends Model
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
        'id_upload', // ✅ TAMBAHAN
        'is_deleted',
        'deleted_at',
        'deleted_by',
        'deletion_reason',
    ];

    protected $casts = [
        'tanggal_spk' => 'date',
        'is_deleted' => 'boolean', // ✅ TAMBAHAN
        'deleted_at' => 'datetime', // ✅ TAMBAHAN
    ];

    // ✅ TAMBAHAN: Relasi ke Upload
    public function upload()
    {
        return $this->belongsTo(Document::class, 'id_upload', 'id_upload');
    }

    // ✅ TAMBAHAN: Relasi ke User (yang menghapus)
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Relasi yang sudah ada
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

    public function sarpenRuangServer()
    {
        return $this->hasOne(SpkSarpenRuangServer::class, 'id_spk');
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

    // public function listItem()
    // {
    //     return $this->hasMany(ListItem::class, 'id_spk');
    // }

    // public function formChecklistWireline()
    // {
    //     return $this->hasOne(FormChecklistWireline::class, 'id_spk');
    // }

    // public function formChecklistWireless()
    // {
    //     return $this->hasOne(FormChecklistWireless::class, 'id_spk');
    // }

    // ✅ TAMBAHAN: Scope untuk filter data yang tidak dihapus
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    // ✅ TAMBAHAN: Scope untuk filter data yang dihapus
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }

    // ✅ TAMBAHAN: Method untuk soft delete
    public function softDelete($userId, $reason = null)
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'deleted_by' => $userId,
            'deletion_reason' => $reason,
        ]);
    }

    // ✅ TAMBAHAN: Method untuk restore soft delete
    public function restore()
    {
        $this->update([
            'is_deleted' => false,
            'deleted_at' => null,
            'deleted_by' => null,
            'deletion_reason' => null,
        ]);
    }
}