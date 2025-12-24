<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwlLog extends Model
{
    protected $table = 'fcwl_log';
    protected $primaryKey = 'id_log';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'date_time',
        'info',
        'photo',
    ];
    
    protected $casts = [
        'date_time' => 'datetime',
        'created_at' => 'datetime',
    ];
    
    // Relationship
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
    
    // Accessor for photo URL if exists
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}