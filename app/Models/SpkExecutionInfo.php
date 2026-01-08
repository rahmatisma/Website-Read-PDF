<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_execution
 * @property int $id_spk
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $pic_pelanggan
 * @property string|null $kontak_pic_pelanggan
 * @property string $teknisi
 * @property string $nama_vendor
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereIdExecution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereKontakPicPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereNamaVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo wherePicPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkExecutionInfo whereTeknisi($value)
 * @mixin \Eloquent
 */
class SpkExecutionInfo extends Model
{
    protected $table = 'spk_execution_info';
    protected $primaryKey = 'id_execution';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'latitude',
        'longitude',
        'pic_pelanggan',
        'kontak_pic_pelanggan',
        'teknisi',
        'nama_vendor',
    ];

    public function spk()
    {
        return $this->belongsTo(SPK::class, 'id_spk', 'id_spk');
    }
}