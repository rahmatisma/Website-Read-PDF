<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwlWaktuPelaksanaan extends Model
{
    protected $table = 'fcwl_waktu_pelaksanaan';
    protected $primaryKey = 'id_waktu';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'jenis_waktu',
        'waktu',
        'keterangan',
    ];
    
    protected $casts = [
        'waktu' => 'datetime',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
}