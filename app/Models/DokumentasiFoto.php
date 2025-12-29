<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_dokumentasi
 * @property int $id_spk
 * @property string $kategori_foto
 * @property string $path_foto
 * @property int|null $urutan
 * @property string|null $keterangan
 * @property string $created_at
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereIdDokumentasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereKategoriFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto wherePathFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DokumentasiFoto whereUrutan($value)
 * @mixin \Eloquent
 */
class DokumentasiFoto extends Model
{
    protected $table = 'dokumentasi_foto';
    protected $primaryKey = 'id_dokumentasi';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'kategori_foto',
        'path_foto',
        'urutan',
        'keterangan',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}