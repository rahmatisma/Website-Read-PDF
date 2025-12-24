<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwChecklistItem extends Model
{
    protected $table = 'fcw_checklist_item';
    protected $primaryKey = 'id_checklist';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'kategori',
        'check_point',
        'standard',
        'nms_engineer',
        'on_site_teknisi',
        'existing',
        'perbaikan',
        'hasil_akhir',
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
    
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori)->orderBy('urutan');
    }
}
