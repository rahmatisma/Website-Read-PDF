<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_tegangan
 * @property int $id_fcw
 * @property string $jenis_sumber
 * @property numeric|null $p_n
 * @property numeric|null $p_g
 * @property numeric|null $n_g
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan whereIdTegangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan whereJenisSumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan whereNG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan wherePG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwTegangan wherePN($value)
 * @mixin \Eloquent
 */
class FcwTegangan extends Model
{
    protected $table = 'fcw_tegangan';
    protected $primaryKey = 'id_tegangan';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'jenis_sumber',
        'p_n',
        'p_g',
        'n_g',
    ];
    
    protected $casts = [
        'p_n' => 'decimal:2',
        'p_g' => 'decimal:2',
        'n_g' => 'decimal:2',
    ];
    
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
    }
}