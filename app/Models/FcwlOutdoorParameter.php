<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_parameter
 * @property int $id_outdoor
 * @property string $kategori
 * @property string $parameter
 * @property string|null $standard
 * @property string|null $existing
 * @property int|null $urutan
 * @property-read \App\Models\FcwlOutdoorArea $outdoorArea
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereExisting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereIdOutdoor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereIdParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorParameter whereUrutan($value)
 * @mixin \Eloquent
 */
class FcwlOutdoorParameter extends Model
{
    protected $table = 'fcwl_outdoor_parameter';
    protected $primaryKey = 'id_parameter';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_outdoor',
        'kategori',
        'parameter',
        'standard',
        'existing',
        'urutan',
    ];
    
    protected $casts = [
        'urutan' => 'integer',
    ];
    
    public function outdoorArea(): BelongsTo
    {
        return $this->belongsTo(FcwlOutdoorArea::class, 'id_outdoor', 'id_outdoor');
    }
}
