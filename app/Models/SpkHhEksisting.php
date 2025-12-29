<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_hh_eksisting
 * @property int $id_spk
 * @property int $nomor_hh
 * @property string|null $kondisi_hh
 * @property string|null $lokasi_hh
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $ketersediaan_closure
 * @property string|null $kapasitas_closure
 * @property string|null $kondisi_closure
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereIdHhEksisting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereKapasitasClosure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereKetersediaanClosure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereKondisiClosure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereKondisiHh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereLokasiHh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkHhEksisting whereNomorHh($value)
 * @mixin \Eloquent
 */
class SpkHhEksisting extends Model
{
    protected $table = 'spk_hh_eksisting';
    protected $primaryKey = 'id_hh_eksisting';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nomor_hh',
        'kondisi_hh',
        'lokasi_hh',
        'latitude',
        'longitude',
        'ketersediaan_closure',
        'kapasitas_closure',
        'kondisi_closure',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}