<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_indoor
 * @property int $id_fcwl
 * @property string|null $merk_ups
 * @property string|null $kapasitas_ups
 * @property string|null $jenis_ups
 * @property string|null $ruangan_bebas_debu
 * @property numeric|null $suhu_ruangan
 * @property string|null $terpasang_ground_bar
 * @property string|null $catuan_input_modem
 * @property numeric|null $v_input_modem_p_n
 * @property numeric|null $v_input_modem_n_g
 * @property string|null $bertumpuk
 * @property string|null $lokasi_ruang
 * @property numeric|null $suhu_casing_modem
 * @property string|null $catuan_input_terbounding
 * @property string|null $splicing_konektor_kabel
 * @property string|null $pemilik_perangkat_cpe
 * @property string|null $jenis_perangkat_cpe
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlIndoorParameter> $parameters
 * @property-read int|null $parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlIndoorParameter> $perangkatCpeParameters
 * @property-read int|null $perangkat_cpe_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlIndoorParameter> $perangkatModemParameters
 * @property-read int|null $perangkat_modem_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlIndoorParameter> $saranaPenunjangParameters
 * @property-read int|null $sarana_penunjang_parameters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereBertumpuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereCatuanInputModem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereCatuanInputTerbounding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereIdIndoor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereJenisPerangkatCpe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereJenisUps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereKapasitasUps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereLokasiRuang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereMerkUps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea wherePemilikPerangkatCpe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereRuanganBebasDebu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereSplicingKonektorKabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereSuhuCasingModem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereSuhuRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereTerpasangGroundBar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereVInputModemNG($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlIndoorArea whereVInputModemPN($value)
 * @mixin \Eloquent
 */
class FcwlIndoorArea extends Model
{
    protected $table = 'fcwl_indoor_area';
    protected $primaryKey = 'id_indoor';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'merk_ups',
        'kapasitas_ups',
        'jenis_ups',
        'ruangan_bebas_debu',
        'suhu_ruangan',
        'terpasang_ground_bar',
        'catuan_input_modem',
        'v_input_modem_p_n',
        'v_input_modem_n_g',
        'bertumpuk',
        'lokasi_ruang',
        'suhu_casing_modem',
        'catuan_input_terbounding',
        'splicing_konektor_kabel',
        'pemilik_perangkat_cpe',
        'jenis_perangkat_cpe',
    ];
    
    protected $casts = [
        'suhu_ruangan' => 'decimal:1',
        'v_input_modem_p_n' => 'decimal:2',
        'v_input_modem_n_g' => 'decimal:2',
        'suhu_casing_modem' => 'decimal:1',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function parameters(): HasMany
    {
        return $this->hasMany(FcwlIndoorParameter::class, 'id_indoor', 'id_indoor');
    }
    
    public function saranaPenunjangParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'sarana_penunjang');
    }
    
    public function perangkatModemParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'perangkat_modem');
    }
    
    public function perangkatCpeParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'perangkat_cpe');
    }
}
