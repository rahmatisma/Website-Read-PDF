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

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}