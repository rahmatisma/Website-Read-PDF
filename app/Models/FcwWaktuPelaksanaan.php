<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_waktu
 * @property int $id_fcw
 * @property string $jenis_waktu
 * @property \Illuminate\Support\Carbon $waktu
 * @property string|null $keterangan
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan whereIdWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan whereJenisWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwWaktuPelaksanaan whereWaktu($value)
 * @mixin \Eloquent
 */
class FcwWaktuPelaksanaan extends Model
{
    protected $table = 'fcw_waktu_pelaksanaan';
    protected $primaryKey = 'id_waktu';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'jenis_waktu',
        'waktu',
        'keterangan',
    ];
    
    protected $casts = [
        'waktu' => 'datetime',
    ];
    
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
    }
}