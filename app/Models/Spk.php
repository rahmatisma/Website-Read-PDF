<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'id_upload', // ✅ Tambahkan ini jika belum ada
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
    // RELATIONSHIPS
    // ========================================
    
    /**
     * ✅ NEW: Relasi ke Upload/Document
     */
    public function upload()
    {
        return $this->belongsTo(Document::class, 'id_upload', 'id_upload');
    }

    /**
     * Relasi ke JARINGAN
     */
    public function jaringan()
    {
        return $this->belongsTo(Jaringan::class, 'no_jaringan', 'no_jaringan');
    }

    // ========================================
    // QUERY SCOPES
    // ========================================
    
    /**
     * Scope untuk SPK aktif (tidak dihapus)
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope untuk SPK yang dihapus
     */
    public function scopeDeleted($query)
    {
        return $query->where('is_deleted', true);
    }

    /**
     * ✅ NEW: Scope untuk SPK biasa (bukan form checklist)
     */
    public function scopeOnlySPK($query)
    {
        return $query->where('document_type', 'spk');
    }

    /**
     * ✅ NEW: Scope untuk Form Checklist saja
     */
    public function scopeOnlyFormChecklist($query)
    {
        return $query->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
    }

    /**
     * ✅ NEW: Scope untuk Form Checklist Wireline
     */
    public function scopeWireline($query)
    {
        return $query->where('document_type', 'form_checklist_wireline');
    }

    /**
     * ✅ NEW: Scope untuk Form Checklist Wireless
     */
    public function scopeWireless($query)
    {
        return $query->where('document_type', 'form_checklist_wireless');
    }

    /**
     * Scope untuk filter berdasarkan jenis SPK
     */
    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_spk', $jenis);
    }

    // ========================================
    // HELPER METHODS
    // ========================================
    
    /**
     * ✅ NEW: Cek apakah SPK ini adalah form checklist
     */
    public function isFormChecklist(): bool
    {
        return in_array($this->document_type, ['form_checklist_wireline', 'form_checklist_wireless']);
    }

    /**
     * ✅ NEW: Cek apakah SPK ini adalah wireline
     */
    public function isWireline(): bool
    {
        return $this->document_type === 'form_checklist_wireline';
    }

    /**
     * ✅ NEW: Cek apakah SPK ini adalah wireless
     */
    public function isWireless(): bool
    {
        return $this->document_type === 'form_checklist_wireless';
    }

    /**
     * Soft delete SPK
     */
    public function softDelete(int $deletedBy, string $reason): bool
    {
        $this->is_deleted = true;
        $this->deleted_at = now();
        $this->deleted_by = $deletedBy;
        $this->deletion_reason = $reason;
        
        return $this->save();
    }

    /**
     * Restore soft deleted SPK
     */
    public function restore(): bool
    {
        $this->is_deleted = false;
        $this->deleted_at = null;
        $this->deleted_by = null;
        $this->deletion_reason = null;
        
        return $this->save();
    }
}