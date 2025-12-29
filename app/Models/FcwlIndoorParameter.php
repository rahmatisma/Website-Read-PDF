<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_parameter
 * @property int $id_indoor
 * @property string $kategori
 * @property string $quality_parameter
 * @property string|null $standard
 * @property string|null $existing
 * @property int|null $urutan
 * @property-read \App\Models\FcwlIndoorArea $indoorArea
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereExisting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereIdIndoor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereIdParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereQualityParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorParameter whereUrutan($value)
 * @mixin \Eloquent
 */
class FcwlIndoorParameter extends Model
{
    protected $table = 'fcwl_indoor_parameter';
    protected $primaryKey = 'id_parameter';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_indoor',
        'kategori',
        'quality_parameter',
        'standard',
        'existing',
        'urutan',
    ];
    
    protected $casts = [
        'urutan' => 'integer',
    ];
    
    public function indoorArea(): BelongsTo
    {
        return $this->belongsTo(FcwlIndoorArea::class, 'id_indoor', 'id_indoor');
    }
}
