<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FcwlOutdoorArea extends Model
{
    protected $table = 'fcwl_outdoor_area';
    protected $primaryKey = 'id_outdoor';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'bs_catuan_sektor',
        'los_ke_bs_catuan',
        'jarak_udara',
        'heading',
        'latitude',
        'longitude',
        'potential_obstacle',
        'type_mounting',
        'mounting_tidak_goyang',
        'center_of_gravity',
        'disekitar_mounting_ada_penangkal_petir',
        'sudut_mounting_terhadap_penangkal_petir',
        'tinggi_mounting',
        'type_penangkal_petir',
    ];
    
    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function parameters(): HasMany
    {
        return $this->hasMany(FcwlOutdoorParameter::class, 'id_outdoor', 'id_outdoor');
    }
    
    public function siteParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'site');
    }
    
    public function saranaPenunjangParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'sarana_penunjang');
    }
    
    public function perangkatAntennaParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'perangkat_antenna');
    }
    
    public function cablingInstallationParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'cabling_installation');
    }
}
