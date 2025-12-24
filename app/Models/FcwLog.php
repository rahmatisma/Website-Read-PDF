<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwLog extends Model
{
    protected $table = 'fcw_log';
    protected $primaryKey = 'id_log';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'date_time',
        'info',
        'photo',
    ];
    
    protected $casts = [
        'date_time' => 'datetime',
        'created_at' => 'datetime',
    ];
    
    // Relationship
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
    }
    
    // Accessor for photo URL if exists
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}