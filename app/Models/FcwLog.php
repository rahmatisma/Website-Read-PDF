<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_log
 * @property int $id_fcw
 * @property \Illuminate\Support\Carbon $date_time
 * @property string $info
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @property-read mixed $photo_url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog whereIdLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwLog wherePhoto($value)
 * @mixin \Eloquent
 */
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