<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_checklist
 * @property int $id_fcw
 * @property string $kategori
 * @property string $check_point
 * @property string|null $standard
 * @property string|null $nms_engineer
 * @property string|null $on_site_teknisi
 * @property string|null $existing
 * @property string|null $perbaikan
 * @property string|null $hasil_akhir
 * @property int|null $urutan
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem byKategori($kategori)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereCheckPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereExisting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereHasilAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereIdChecklist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereNmsEngineer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereOnSiteTeknisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem wherePerbaikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereStandard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwChecklistItem whereUrutan($value)
 * @mixin \Eloquent
 */
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
