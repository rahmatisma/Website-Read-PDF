<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_splitter
 * @property int $id_spk
 * @property string|null $lokasi_splitter
 * @property string|null $id_splitter_text
 * @property string|null $kapasitas_splitter
 * @property string|null $jumlah_port_kosong
 * @property string|null $list_port_kosong_dan_redaman
 * @property string|null $nama_node_jika_tidak_ada_splitter
 * @property string|null $list_port_kosong
 * @property string|null $arah_akses
 * @property-read \App\Models\SPK $spk
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereArahAkses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereIdSplitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereIdSplitterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereJumlahPortKosong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereKapasitasSplitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereListPortKosong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereListPortKosongDanRedaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereLokasiSplitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpkDataSplitter whereNamaNodeJikaTidakAdaSplitter($value)
 * @mixin \Eloquent
 */
class SpkDataSplitter extends Model
{
    protected $table = 'spk_data_splitter';
    protected $primaryKey = 'id_splitter';
    public $timestamps = false;

    protected $fillable = [
        'id_spk',
        'lokasi_splitter',
        'id_splitter_text',
        'kapasitas_splitter',
        'jumlah_port_kosong',
        'list_port_kosong_dan_redaman',
        'nama_node_jika_tidak_ada_splitter',
        'list_port_kosong',
        'arah_akses',
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk');
    }
}