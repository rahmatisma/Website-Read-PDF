<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkEmbedding extends Model
{
    protected $table = 'spk_embeddings';
    protected $primaryKey = 'id_embedding';

    protected $fillable = [
        'id_spk',
        'no_spk',
        'content_text',
        'embedding',
        'embedding_model',
        'embedding_dimension',
    ];

    protected $casts = [
        'id_spk' => 'integer',
        'embedding' => 'array',
        'embedding_dimension' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke SPK
     */
    public function spk(): BelongsTo
    {
        return $this->belongsTo(Spk::class, 'id_spk', 'id_spk');
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