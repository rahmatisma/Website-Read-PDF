<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'uploads';
    protected $primaryKey = 'id_upload';
    public $timestamps = true;

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
        'extracted_data', // ⬅️ pastikan ada ini
    ];

    // ⬅️ TAMBAH BAGIAN INI
    protected $casts = [
        'extracted_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}