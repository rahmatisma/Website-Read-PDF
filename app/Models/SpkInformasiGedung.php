<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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