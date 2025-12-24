<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
