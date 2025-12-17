<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JaringanEmbedding extends Model
{
    protected $table = 'jaringan_embeddings';
    protected $primaryKey = 'id_embedding';

    protected $fillable = [
        'no_jaringan',
        'content_text',
        'embedding',
        'embedding_model',
        'embedding_dimension',
    ];

    protected $casts = [
        'embedding' => 'array',
        'embedding_dimension' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Jaringan
     */
    public function jaringan(): BelongsTo
    {
        return $this->belongsTo(Jaringan::class, 'no_jaringan', 'no_jaringan');
    }

    /**
     * Get embedding as array
     */
    public function getEmbeddingArray(): array
    {
        if (is_string($this->embedding)) {
            return json_decode($this->embedding, true) ?? [];
        }
        return $this->embedding ?? [];
    }
}