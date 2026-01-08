<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function spk()
    {
        return $this->belongsTo(\App\Models\Spk::class, 'id_spk', 'id_spk');
    }
}