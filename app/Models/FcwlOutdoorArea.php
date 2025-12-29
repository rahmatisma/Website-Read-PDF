<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_outdoor
 * @property int $id_fcwl
 * @property string|null $bs_catuan_sektor
 * @property string|null $los_ke_bs_catuan
 * @property string|null $jarak_udara
 * @property string|null $heading
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $potential_obstacle
 * @property string|null $type_mounting
 * @property string|null $mounting_tidak_goyang
 * @property string|null $center_of_gravity
 * @property string|null $disekitar_mounting_ada_penangkal_petir
 * @property string|null $sudut_mounting_terhadap_penangkal_petir
 * @property string|null $tinggi_mounting
 * @property string|null $type_penangkal_petir
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlOutdoorParameter> $cablingInstallationParameters
 * @property-read int|null $cabling_installation_parameters_count
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlOutdoorParameter> $parameters
 * @property-read int|null $parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlOutdoorParameter> $perangkatAntennaParameters
 * @property-read int|null $perangkat_antenna_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlOutdoorParameter> $saranaPenunjangParameters
 * @property-read int|null $sarana_penunjang_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlOutdoorParameter> $siteParameters
 * @property-read int|null $site_parameters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereBsCatuanSektor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereCenterOfGravity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereDisekitarMountingAdaPenangkalPetir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereIdOutdoor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereJarakUdara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereLosKeBsCatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereMountingTidakGoyang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea wherePotentialObstacle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereSudutMountingTerhadapPenangkalPetir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereTinggiMounting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereTypeMounting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlOutdoorArea whereTypePenangkalPetir($value)
 * @mixin \Eloquent
 */
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
