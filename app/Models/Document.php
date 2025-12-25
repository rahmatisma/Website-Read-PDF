<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // ✅ Default value untuk status saat create
    protected $attributes = [
        'status' => 'uploaded',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // ✅ Scope untuk filter berdasarkan status
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

    // ✅ Helper untuk cek status
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
}