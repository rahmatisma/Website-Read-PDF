<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwGuidanceFoto extends Model
{
    protected $table = 'fcw_guidance_foto';
    protected $primaryKey = 'id_guidance';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'jenis_foto',
        'path_foto',
        'urutan',
    ];
    
    protected $casts = [
        'urutan' => 'integer',
        'created_at' => 'datetime',
    ];
    
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
    }
    
    public function getFullPathAttribute()
    {
        return storage_path('app/public/' . $this->path_foto);
    }
    
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path_foto);
    }
}
