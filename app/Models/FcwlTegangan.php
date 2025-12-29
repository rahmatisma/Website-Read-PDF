<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_tegangan
 * @property int $id_fcwl
 * @property string $jenis_sumber
 * @property numeric|null $p_n
 * @property numeric|null $p_g
 * @property numeric|null $n_g
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan whereIdTegangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan whereJenisSumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan whereNG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan wherePG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlTegangan wherePN($value)
 * @mixin \Eloquent
 */
class FcwlTegangan extends Model
{
    protected $table = 'fcwl_tegangan';
    protected $primaryKey = 'id_tegangan';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
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
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
}
