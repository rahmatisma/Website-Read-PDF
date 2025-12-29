<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_perizinan_gedung
 * @property int $id_spk
 * @property string|null $pic_bm
 * @property string|null $kontak_pic_bm
 * @property string|null $material_dan_infrastruktur
 * @property string|null $panjang_kabel_dalam_gedung
 * @property string|null $pelaksana_penarikan_kabel_dalam_gedung
 * @property string|null $waktu_pelaksanaan_penarikan_kabel
 * @property string|null $supervisi
 * @property string|null $deposit_kerja
 * @property string|null $ikg_instalasi_kabel_gedung
 * @property string|null $biaya_sewa
 * @property string|null $biaya_lain
 * @property string|null $info_lain_lain_jika_ada
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereBiayaLain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereBiayaSewa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereDepositKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereIdPerizinanGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereIkgInstalasiKabelGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereInfoLainLainJikaAda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereKontakPicBm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereMaterialDanInfrastruktur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung wherePanjangKabelDalamGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung wherePelaksanaPenarikanKabelDalamGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung wherePicBm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereSupervisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaGedung whereWaktuPelaksanaanPenarikanKabel($value)
 * @mixin \Eloquent
 */
class SpkPerizinanBiayaGedung extends Model
{
    protected $table = 'spk_perizinan_biaya_gedung';
    protected $primaryKey = 'id_perizinan_gedung';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'pic_bm',
        'kontak_pic_bm',
        'material_dan_infrastruktur',
        'panjang_kabel_dalam_gedung',
        'pelaksana_penarikan_kabel_dalam_gedung',
        'waktu_pelaksanaan_penarikan_kabel',
        'supervisi',
        'deposit_kerja',
        'ikg_instalasi_kabel_gedung',
        'biaya_sewa',
        'biaya_lain',
        'info_lain_lain_jika_ada',
    ];

    public function spk()
    {
        return $this->belongsTo(SPK::class, 'id_spk');
    }
}
