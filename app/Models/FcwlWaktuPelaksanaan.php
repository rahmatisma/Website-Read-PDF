<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_waktu
 * @property int $id_fcwl
 * @property string $jenis_waktu
 * @property \Illuminate\Support\Carbon $waktu
 * @property string|null $keterangan
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan whereIdWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan whereJenisWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlWaktuPelaksanaan whereWaktu($value)
 * @mixin \Eloquent
 */
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