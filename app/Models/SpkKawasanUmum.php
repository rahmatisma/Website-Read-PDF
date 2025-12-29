<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_kawasan_umum
 * @property int $id_spk
 * @property string|null $nama_kawasan_umum_pu_yang_dilewati
 * @property string|null $panjang_jalur_outdoor_di_kawasan_umum
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum whereIdKawasanUmum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum whereNamaKawasanUmumPuYangDilewati($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkKawasanUmum wherePanjangJalurOutdoorDiKawasanUmum($value)
 * @mixin \Eloquent
 */
class SpkKawasanUmum extends Model
{
    protected $table = 'spk_kawasan_umum';
    protected $primaryKey = 'id_kawasan_umum';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'nama_kawasan_umum_pu_yang_dilewati',
        'panjang_jalur_outdoor_di_kawasan_umum',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}