<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_lokasi_antena
 * @property int $id_spk
 * @property string|null $lokasi_antena
 * @property string|null $detail_lokasi_antena
 * @property string|null $space_tersedia
 * @property string|null $akses_di_lokasi_perlu_alat_bantu
 * @property string|null $penangkal_petir
 * @property string|null $tinggi_penangkal_petir
 * @property string|null $jarak_ke_lokasi_antena
 * @property string|null $tindak_lanjut
 * @property string|null $tower_pole
 * @property string|null $pemilik_tower_pole
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereAksesDiLokasiPerluAlatBantu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereDetailLokasiAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereIdLokasiAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereJarakKeLokasiAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereLokasiAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena wherePemilikTowerPole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena wherePenangkalPetir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereSpaceTersedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereTindakLanjut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereTinggiPenangkalPetir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkLokasiAntena whereTowerPole($value)
 * @mixin \Eloquent
 */
class SpkLokasiAntena extends Model
{
    protected $table = 'spk_lokasi_antena';
    protected $primaryKey = 'id_lokasi_antena';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_antena',
        'detail_lokasi_antena',
        'space_tersedia',
        'akses_di_lokasi_perlu_alat_bantu',
        'penangkal_petir',
        'tinggi_penangkal_petir',
        'jarak_ke_lokasi_antena',
        'tindak_lanjut',
        'tower_pole',
        'pemilik_tower_pole',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}