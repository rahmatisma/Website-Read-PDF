<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_berita_acara
 * @property int $id_spk
 * @property string $judul_spk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara whereIdBeritaAcara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara whereJudulSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BeritaAcara whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BeritaAcara extends Model
{
    protected $table = 'berita_acara';
    protected $primaryKey = 'id_berita_acara';

    protected $fillable = [
        'id_spk',
        'judul_spk',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}