<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_guidance
 * @property int $id_fcw
 * @property string $jenis_foto
 * @property string $path_foto
 * @property int|null $urutan
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @property-read mixed $full_path
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto whereIdGuidance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto whereJenisFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto wherePathFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwGuidanceFoto whereUrutan($value)
 * @mixin \Eloquent
 */
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
