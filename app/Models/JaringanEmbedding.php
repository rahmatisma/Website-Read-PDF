<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JaringanEmbedding extends Model
{
    protected $table = 'jaringan_embeddings';
    protected $primaryKey = 'id_embedding';

    protected $fillable = [
        'no_jaringan',
        'content_text',
        'embedding',
        'embedding_model',
        'embedding_dimension'
    ];

    protected $casts = [
        'embedding' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getEmbeddingArray(): array
    {
        if (empty($this->embedding)) {
            return [];
        }
        return is_array($this->embedding) ? $this->embedding : [];
    }

    public function jaringan()
    {
        return $this->belongsTo(\App\Models\Jaringan::class, 'no_jaringan', 'no_jaringan');
    }
}