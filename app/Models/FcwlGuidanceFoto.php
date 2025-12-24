<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwlGuidanceFoto extends Model
{
    protected $table = 'fcwl_guidance_foto';
    protected $primaryKey = 'id_guidance';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'jenis_foto',
        'path_foto',
        'urutan',
    ];
    
    protected $casts = [
        'urutan' => 'integer',
        'created_at' => 'datetime',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
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
