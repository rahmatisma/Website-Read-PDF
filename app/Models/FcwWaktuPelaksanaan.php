<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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