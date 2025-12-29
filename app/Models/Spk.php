<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Core / Related Models
use App\Models\Jaringan;
use App\Models\Document;

// SPK Related
use App\Models\SpkPelaksanaan;
use App\Models\SpkExecutionInfo;
use App\Models\SpkInformasiGedung;
use App\Models\SpkSarpenRuangServer;
use App\Models\SpkSarpenTegangan;
use App\Models\SpkLokasiAntena;
use App\Models\SpkPerizinanBiayaGedung;
use App\Models\SpkPenempatanPerangkat;
use App\Models\SpkPerizinanBiayaKawasan;
use App\Models\SpkKawasanUmum;
use App\Models\SpkDataSplitter;
use App\Models\SpkHHEksisting;
use App\Models\SpkHHBaru;
use App\Models\DokumentasiFoto;
use App\Models\BeritaAcara;
use App\Models\ListItem;

// Form Checklist
use App\Models\FormChecklistWireline;
use App\Models\FormChecklistWireless;

class SPK extends Model
{
    protected $table = 'SPK';
    protected $primaryKey = 'id_spk';

    protected $fillable = [
        'no_spk',
        'no_jaringan',
        'document_type',
        'jenis_spk',
        'tanggal_spk',
        'no_mr',
        'no_fps',
        'id_upload',
        'is_deleted',
        'deleted_at',
        'deleted_by',
        'deletion_reason',
    ];

    protected $casts = [
        'tanggal_spk' => 'date',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ========================================
    // RELATIONS - SEMUA SUDAH DIPERBAIKI ✅
    // ========================================

    public function jaringan()
    {
        return $this->belongsTo(Jaringan::class, 'no_jaringan', 'no_jaringan');
    }

    public function upload()
    {
        return $this->belongsTo(Document::class, 'id_upload', 'id_upload');
    }

    public function pelaksanaan()
    {
        return $this->hasOne(SpkPelaksanaan::class, 'id_spk', 'id_spk'); // ✅
    }

    public function executionInfo()
    {
        return $this->hasOne(SpkExecutionInfo::class, 'id_spk', 'id_spk'); // ✅
    }

    public function informasiGedung()
    {
        return $this->hasOne(SpkInformasiGedung::class, 'id_spk', 'id_spk'); // ✅
    }

    public function sarpenRuangServer()
    {
        return $this->hasOne(SpkSarpenRuangServer::class, 'id_spk', 'id_spk'); // ✅
    }

    public function sarpenTegangan()
    {
        return $this->hasManyThrough(
            SpkSarpenRuangServer::class, // ✅
            'id_spk',
            'id_sarpen',
            'id_spk',
            'id_sarpen'
        );
    }

    public function lokasiAntena()
    {
        return $this->hasOne(SpkLokasiAntena::class, 'id_spk', 'id_spk'); // ✅
    }

    public function perizinanGedung()
    {
        return $this->hasOne(SpkPerizinanBiayaGedung::class, 'id_spk', 'id_spk'); // ✅
    }

    public function penempatanPerangkat()
    {
        return $this->hasOne(SpkPenempatanPerangkat::class, 'id_spk', 'id_spk'); // ✅
    }

    public function perizinanKawasan()
    {
        return $this->hasOne(SpkPerizinanBiayaKawasan::class, 'id_spk', 'id_spk'); // ✅
    }

    public function kawasanUmum()
    {
        return $this->hasOne(SpkKawasanUmum::class, 'id_spk', 'id_spk'); // ✅
    }

    public function dataSplitter()
    {
        return $this->hasOne(SpkDataSplitter::class, 'id_spk', 'id_spk'); // ✅
    }

    public function hhEksisting()
    {
        return $this->hasMany(SpkHHEksisting::class, 'id_spk', 'id_spk'); // ✅
    }

    public function hhBaru()
    {
        return $this->hasMany(SpkHHBaru::class, 'id_spk', 'id_spk'); // ✅
    }

    public function dokumentasiFoto()
    {
        return $this->hasMany(DokumentasiFoto::class, 'id_spk', 'id_spk');
    }

    public function beritaAcara()
    {
        return $this->hasOne(BeritaAcara::class, 'id_spk', 'id_spk');
    }

    public function listItem()
    {
        return $this->hasMany(ListItem::class, 'id_spk', 'id_spk');
    }

    public function checklistWireline()
    {
        return $this->hasOne(FormChecklistWireline::class, 'id_spk', 'id_spk');
    }

    public function checklistWireless()
    {
        return $this->hasOne(FormChecklistWireless::class, 'id_spk', 'id_spk');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }

    public function scopeOnlySPK($query)
    {
        return $query->where('document_type', 'spk');
    }

    public function scopeOnlyFormChecklist($query)
    {
        return $query->whereIn('document_type', ['formchecklistwireline', 'formchecklistwireless']);
    }

    public function scopeWireline($query)
    {
        return $query->where('document_type', 'formchecklistwireline');
    }

    public function scopeWireless($query)
    {
        return $query->where('document_type', 'formchecklistwireless');
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_spk', $jenis);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    public function isFormChecklist(): bool
    {
        return in_array($this->document_type, ['formchecklistwireline', 'formchecklistwireless']);
    }

    public function isWireline(): bool
    {
        return $this->document_type === 'formchecklistwireline';
    }

    public function isWireless(): bool
    {
        return $this->document_type === 'formchecklistwireless';
    }

    public function softDelete(int $deletedBy, string $reason): bool
    {
        $this->is_deleted = true;
        $this->deleted_at = now();
        $this->deleted_by = $deletedBy;
        $this->deletion_reason = $reason;

        return $this->save();
    }

    public function restore(): bool
    {
        $this->is_deleted = false;
        $this->deleted_at = null;
        $this->deleted_by = null;
        $this->deletion_reason = null;

        return $this->save();
    }
}
