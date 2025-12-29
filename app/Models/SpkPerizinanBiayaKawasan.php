<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_perizinan_kawasan
 * @property int $id_spk
 * @property string $melewati_kawasan_private
 * @property string|null $nama_kawasan
 * @property string|null $pic_kawasan
 * @property string|null $kontak_pic_kawasan
 * @property string|null $panjang_kabel_dalam_kawasan
 * @property string|null $pelaksana_penarikan_kabel_dalam_kawasan
 * @property string|null $deposit_kerja
 * @property string|null $supervisi
 * @property string|null $biaya_penarikan_kabel_dalam_kawasan
 * @property string|null $biaya_sewa
 * @property string|null $biaya_lain
 * @property string|null $info_lain_lain_jika_ada
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereBiayaLain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereBiayaPenarikanKabelDalamKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereBiayaSewa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereDepositKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereIdPerizinanKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereInfoLainLainJikaAda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereKontakPicKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereMelewatiKawasanPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereNamaKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan wherePanjangKabelDalamKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan wherePelaksanaPenarikanKabelDalamKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan wherePicKawasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkPerizinanBiayaKawasan whereSupervisi($value)
 * @mixin \Eloquent
 */
class SpkPerizinanBiayaKawasan extends Model
{
    protected $table = 'spk_perizinan_biaya_kawasan';
    protected $primaryKey = 'id_perizinan_kawasan';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'melewati_kawasan_private',
        'nama_kawasan',
        'pic_kawasan',
        'kontak_pic_kawasan',
        'panjang_kabel_dalam_kawasan',
        'pelaksana_penarikan_kabel_dalam_kawasan',
        'deposit_kerja',
        'supervisi',
        'biaya_penarikan_kabel_dalam_kawasan',
        'biaya_sewa',
        'biaya_lain',
        'info_lain_lain_jika_ada',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}