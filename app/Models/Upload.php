<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $table = 'uploads'; // tabel yang dipakai

    protected $primaryKey = 'id_upload'; // primary key

    public $timestamps = true; // created_at & updated_at otomatis

    protected $fillable = [
        'source_type',
        'id_user',
        'source_system',
        'document_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'tipe_dokumen'
    ];
}
