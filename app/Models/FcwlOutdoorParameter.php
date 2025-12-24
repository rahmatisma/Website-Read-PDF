<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
