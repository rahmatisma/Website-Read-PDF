<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_guidance
 * @property int $id_fcwl
 * @property string $jenis_foto
 * @property string $path_foto
 * @property int|null $urutan
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @property-read mixed $full_path
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto whereIdGuidance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto whereJenisFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto wherePathFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlGuidanceFoto whereUrutan($value)
 * @mixin \Eloquent
 */
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
