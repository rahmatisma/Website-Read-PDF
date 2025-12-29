<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_hh_baru
 * @property int $id_spk
 * @property int $nomor_hh
 * @property string|null $lokasi_hh
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $kebutuhan_penambahan_closure
 * @property string|null $kapasitas_closure
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereIdHhBaru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereKapasitasClosure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereKebutuhanPenambahanClosure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereLokasiHh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhBaru whereNomorHh($value)
 * @mixin \Eloquent
 */
class SpkHhBaru extends Model
{
    protected $table = 'spk_hh_baru';
    protected $primaryKey = 'id_hh_baru';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nomor_hh',
        'lokasi_hh',
        'latitude',
        'longitude',
        'kebutuhan_penambahan_closure',
        'kapasitas_closure',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}