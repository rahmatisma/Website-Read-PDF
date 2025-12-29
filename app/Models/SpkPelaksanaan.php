<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_pelaksanaan
 * @property int $id_spk
 * @property \Illuminate\Support\Carbon $permintaan_pelanggan
 * @property \Illuminate\Support\Carbon|null $datang
 * @property \Illuminate\Support\Carbon|null $selesai
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan whereDatang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan whereIdPelaksanaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan wherePermintaanPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPelaksanaan whereSelesai($value)
 * @mixin \Eloquent
 */
class SpkPelaksanaan extends Model
{
    protected $table = 'spk_pelaksanaan';
    protected $primaryKey = 'id_pelaksanaan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'permintaan_pelanggan',
        'datang',
        'selesai',
    ];

    protected $casts = [
        'permintaan_pelanggan' => 'datetime',
        'datang' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}