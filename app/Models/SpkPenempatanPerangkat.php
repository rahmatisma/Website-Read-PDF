<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_penempatan
 * @property int $id_spk
 * @property string|null $lokasi_penempatan_modem_dan_router
 * @property string|null $kesiapan_ruang_server
 * @property string|null $ketersedian_rak_server
 * @property string|null $space_modem_dan_router
 * @property string|null $diizinkan_foto_ruang_server_pelanggan
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereDiizinkanFotoRuangServerPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereIdPenempatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereKesiapanRuangServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereKetersedianRakServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereLokasiPenempatanModemDanRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPenempatanPerangkat whereSpaceModemDanRouter($value)
 * @mixin \Eloquent
 */
class SpkPenempatanPerangkat extends Model
{
    protected $table = 'spk_penempatan_perangkat';
    protected $primaryKey = 'id_penempatan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_penempatan_modem_dan_router',
        'kesiapan_ruang_server',
        'ketersedian_rak_server',
        'space_modem_dan_router',
        'diizinkan_foto_ruang_server_pelanggan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}