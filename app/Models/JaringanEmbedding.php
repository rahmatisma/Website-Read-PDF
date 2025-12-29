<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_embedding
 * @property string $no_jaringan
 * @property string $content_text
 * @property array<array-key, mixed> $embedding
 * @property string $embedding_model
 * @property int $embedding_dimension
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Jaringan $jaringan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereContentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereEmbeddingDimension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereEmbeddingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereIdEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereNoJaringan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JaringanEmbedding whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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