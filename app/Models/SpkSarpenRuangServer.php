<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_sarpen
 * @property int $id_spk
 * @property string|null $power_line_listrik
 * @property string|null $ketersediaan_power_outlet
 * @property string|null $grounding_listrik
 * @property string|null $ups
 * @property string|null $ruangan_ber_ac
 * @property numeric|null $suhu_ruangan_value
 * @property string|null $suhu_ruangan_keterangan
 * @property string|null $lantai
 * @property string|null $ruang
 * @property string|null $perangkat_pelanggan
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereGroundingListrik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereIdSarpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereKetersediaanPowerOutlet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereLantai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer wherePerangkatPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer wherePowerLineListrik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereRuang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereRuanganBerAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereSuhuRuanganKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereSuhuRuanganValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkSarpenRuangServer whereUps($value)
 * @mixin \Eloquent
 */
class SpkSarpenRuangServer extends Model
{
    protected $table = 'spk_sarpen_ruang_server';
    protected $primaryKey = 'id_sarpen';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'power_line_listrik',
        'ketersediaan_power_outlet',
        'grounding_listrik',
        'ups',
        'ruangan_ber_ac',
        'suhu_ruangan_value',
        'suhu_ruangan_keterangan',
        'lantai',
        'ruang',
        'perangkat_pelanggan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}