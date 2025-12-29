<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_embedding
 * @property int $id_spk
 * @property string $no_spk
 * @property string $content_text
 * @property array<array-key, mixed> $embedding
 * @property string $embedding_model
 * @property int $embedding_dimension
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereContentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereEmbeddingDimension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereEmbeddingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereIdEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereNoSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkEmbedding whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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