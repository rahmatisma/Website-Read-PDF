<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_cabling
 * @property int $id_fcwl
 * @property string|null $type_kabel_ifl
 * @property string|null $panjang_kabel_ifl
 * @property string|null $tahanan_short_kabel_ifl
 * @property string|null $terpasang_arrestor
 * @property string|null $splicing_konektor_kabel_ifl
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereIdCabling($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation wherePanjangKabelIfl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereSplicingKonektorKabelIfl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereTahananShortKabelIfl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereTerpasangArrestor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlCablingInstallation whereTypeKabelIfl($value)
 * @mixin \Eloquent
 */
class FcwlCablingInstallation extends Model
{
    protected $table = 'fcwl_cabling_installation';
    protected $primaryKey = 'id_cabling';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'type_kabel_ifl',
        'panjang_kabel_ifl',
        'tahanan_short_kabel_ifl',
        'terpasang_arrestor',
        'splicing_konektor_kabel_ifl',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
}
