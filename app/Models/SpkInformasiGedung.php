<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_info_gedung
 * @property int $id_spk
 * @property string $alamat
 * @property string|null $status_gedung
 * @property string|null $kondisi_gedung
 * @property string|null $pemilik_bangunan
 * @property string|null $kontak_person
 * @property string|null $bagian_jabatan
 * @property string|null $telpon_fax
 * @property string|null $email
 * @property int|null $jumlah_lantai_gedung
 * @property string|null $pelanggan_fo
 * @property string|null $penempatan_antena
 * @property string|null $sewa_space_antena
 * @property string|null $sewa_shaft_kabel
 * @property string|null $biaya_ikg
 * @property string|null $penanggungjawab_sewa
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereBagianJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereBiayaIkg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereIdInfoGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereJumlahLantaiGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereKondisiGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereKontakPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung wherePelangganFo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung wherePemilikBangunan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung wherePenanggungjawabSewa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung wherePenempatanAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereSewaShaftKabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereSewaSpaceAntena($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereStatusGedung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkInformasiGedung whereTelponFax($value)
 * @mixin \Eloquent
 */
class SpkInformasiGedung extends Model
{
    protected $table = 'spk_informasi_gedung';
    protected $primaryKey = 'id_info_gedung';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'alamat',
        'status_gedung',
        'kondisi_gedung',
        'pemilik_bangunan',
        'kontak_person',
        'bagian_jabatan',
        'telpon_fax',
        'email',
        'jumlah_lantai_gedung',
        'pelanggan_fo',
        'penempatan_antena',
        'sewa_space_antena',
        'sewa_shaft_kabel',
        'biaya_ikg',
        'penanggungjawab_sewa',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}