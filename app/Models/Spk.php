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

    // ========== RELATIONSHIPS ==========

    public function upload()
    {
        return $this->belongsTo(Document::class, 'id_upload', 'id_upload');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function jaringan()
    {
        return $this->belongsTo(Jaringan::class, 'no_jaringan', 'no_jaringan');
    }

    // ========== TAMBAHKAN RELATIONSHIP KE EMBEDDING ==========
    
    /**
     * Relasi ke SpkEmbedding (one-to-one)
     */
    public function embedding()
    {
        return $this->hasOne(SpkEmbedding::class, 'id_spk', 'id_spk');
    }

    /**
     * Atau jika multiple embeddings (one-to-many)
     */
    public function embeddings()
    {
        return $this->hasMany(SpkEmbedding::class, 'id_spk', 'id_spk');
    }

    /**
     * Method helper untuk generate text yang akan di-embed
     */
    public function getEmbeddableText(): string
    {
        $parts = [
            'No SPK: ' . $this->no_spk,
            'No Jaringan: ' . $this->no_jaringan,
            'Jenis SPK: ' . $this->jenis_spk,
            'Document Type: ' . $this->document_type,
            'Tanggal SPK: ' . optional($this->tanggal_spk)->format('Y-m-d'),
        ];

        // Tambahkan info dari relasi jika ada
        if ($this->jaringan) {
            $parts[] = 'Nama Pelanggan: ' . $this->jaringan->nama_pelanggan;
            $parts[] = 'Lokasi: ' . $this->jaringan->lokasi_pelanggan;
        }

        if ($this->no_mr) {
            $parts[] = 'No MR: ' . $this->no_mr;
        }

        if ($this->no_fps) {
            $parts[] = 'No FPS: ' . $this->no_fps;
        }

        return implode(' | ', array_filter($parts));
    }

    // ========== EXISTING RELATIONSHIPS ==========

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

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }

    // ========== METHODS ==========

    public function softDelete($userId, $reason = null)
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'deleted_by' => $userId,
            'deletion_reason' => $reason,
        ]);
    }

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