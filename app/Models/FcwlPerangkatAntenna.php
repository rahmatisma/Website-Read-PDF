<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\FormChecklistWireless|null $formChecklistWireless
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlPerangkatAntenna newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlPerangkatAntenna newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlPerangkatAntenna query()
 * @mixin \Eloquent
 */
class FcwlPerangkatAntenna extends Model
{
    protected $table = 'fcwl_perangkat_antenna';
    protected $primaryKey = 'id_antenna';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'polarisasi',
        'altitude',
        'lokasi',
        'antenna_terbounding_dengan_ground',
        'posisi_antena_sejajar',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
}
