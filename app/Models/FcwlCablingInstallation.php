<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
