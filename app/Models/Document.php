<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_upload
 * @property string $source_type
 * @property int|null $id_user
 * @property string|null $source_system
 * @property string $document_type
 * @property string $file_name
 * @property string $file_path
 * @property string $file_type
 * @property int|null $file_size
 * @property string $status
 * @property array<array-key, mixed>|null $extracted_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SPK> $spks
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document processing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document uploaded()
 * @mixin \Eloquent
 */
class Document extends Model
{
    protected $table = 'uploads';
    protected $primaryKey = 'id_upload';
    
    protected $fillable = [
        'source_type',
        'id_user',
        'source_system',
        'document_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'extracted_data',
    ];

    protected $casts = [
        'extracted_data' => 'array'
    ];

    protected $attributes = [
        'status' => 'uploaded',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================
    
    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     *  NEW: Relasi ke SPK (1 upload bisa punya banyak SPK)
     * Contoh: 1 PDF bisa berisi data untuk beberapa SPK
     */
    public function spks()
    {
        return $this->hasMany(SPK::class, 'id_upload', 'id_upload');
    }

    // ========================================
    // QUERY SCOPES
    // ========================================
    
    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
    }

    /**
     *  NEW: Scope untuk dokumen SPK (bukan form checklist)
     */
    public function scopeIsSPK($query)
    {
        return $query->whereHas('spks', function($q) {
            $q->where('document_type', 'spk');
        });
    }

    /**
     *  NEW: Scope untuk dokumen Form Checklist
     */
    public function scopeIsFormChecklist($query)
    {
        return $query->whereHas('spks', function($q) {
            $q->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless']);
        });
    }

    /**
     *  NEW: Scope untuk dokumen Form Checklist Wireline
     */
    public function scopeIsFormChecklistWireline($query)
    {
        return $query->whereHas('spks', function($q) {
            $q->where('document_type', 'form_checklist_wireline');
        });
    }

    /**
     *  NEW: Scope untuk dokumen Form Checklist Wireless
     */
    public function scopeIsFormChecklistWireless($query)
    {
        return $query->whereHas('spks', function($q) {
            $q->where('document_type', 'form_checklist_wireless');
        });
    }

    // ========================================
    // HELPER METHODS
    // ========================================
    
    /**
     * Cek status dokumen
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isUploaded(): bool
    {
        return $this->status === 'uploaded';
    }

    /**
     *  NEW: Cek apakah dokumen ini adalah SPK
     */
    public function isSPKDocument(): bool
    {
        return $this->spks()->where('document_type', 'spk')->exists();
    }

    /**
     *  NEW: Cek apakah dokumen ini adalah Form Checklist
     */
    public function isFormChecklistDocument(): bool
    {
        return $this->spks()
            ->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless'])
            ->exists();
    }

    /**
     *  NEW: Get tipe form checklist (wireline/wireless)
     */
    public function getFormChecklistType(): ?string
    {
        $spk = $this->spks()
            ->whereIn('document_type', ['form_checklist_wireline', 'form_checklist_wireless'])
            ->first();
        
        return $spk ? $spk->document_type : null;
    }
}