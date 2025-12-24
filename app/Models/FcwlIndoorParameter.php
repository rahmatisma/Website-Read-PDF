<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
