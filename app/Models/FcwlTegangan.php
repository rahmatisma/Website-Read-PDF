<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
