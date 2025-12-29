<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $no_jaringan
 * @property string $nama_pelanggan
 * @property string $lokasi_pelanggan
 * @property string $jasa
 * @property string|null $media_akses
 * @property string|null $kecepatan
 * @property string|null $manage_router
 * @property string|null $opsi_router
 * @property string|null $ip_lan
 * @property string|null $kode_jaringan
 * @property string|null $no_fmb
 * @property string|null $pop
 * @property \Illuminate\Support\Carbon|null $tgl_rfs_la
 * @property \Illuminate\Support\Carbon|null $tgl_rfs_plg
 * @property int $is_deleted
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property string|null $deletion_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JaringanEmbedding|null $embedding
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JaringanEmbedding> $embeddings
 * @property-read int|null $embeddings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SPK> $spk
 * @property-read int|null $spk_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereDeletionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereIpLan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereJasa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereKecepatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereKodeJaringan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereLokasiPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereManageRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereMediaAkses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereNamaPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereNoFmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereNoJaringan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereOpsiRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan wherePop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereTglRfsLa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereTglRfsPlg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jaringan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jaringan extends Model
{
    protected $table = 'jaringan';
    protected $primaryKey = 'no_jaringan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_jaringan',
        'nama_pelanggan',
        'lokasi_pelanggan',
        'jasa',
        'media_akses',
        'kecepatan',
        'manage_router',
        'opsi_router',
        'ip_lan',
        'kode_jaringan',
        'no_fmb',
        'pop',
        'tgl_rfs_la',
        'tgl_rfs_plg',
    ];

    protected $casts = [
        'tgl_rfs_la' => 'date',
        'tgl_rfs_plg' => 'date',
    ];

    public function spk()
    {
        return $this->hasMany(Spk::class, 'no_jaringan', 'no_jaringan');
    }

    // ========== TAMBAHKAN RELATIONSHIP KE EMBEDDING ==========
    
    /**
     * Relasi ke JaringanEmbedding (one-to-one)
     */
    public function embedding()
    {
        return $this->hasOne(JaringanEmbedding::class, 'no_jaringan', 'no_jaringan');
    }

    /**
     * Atau jika satu jaringan bisa punya multiple embeddings (one-to-many)
     */
    public function embeddings()
    {
        return $this->hasMany(JaringanEmbedding::class, 'no_jaringan', 'no_jaringan');
    }

    /**
     * Method helper untuk generate text yang akan di-embed
     */
    public function getEmbeddableText(): string
    {
        return implode(' ', array_filter([
            'No Jaringan: ' . $this->no_jaringan,
            'Nama Pelanggan: ' . $this->nama_pelanggan,
            'Lokasi: ' . $this->lokasi_pelanggan,
            'Jasa: ' . $this->jasa,
            'Media Akses: ' . $this->media_akses,
            'Kecepatan: ' . $this->kecepatan,
            'POP: ' . $this->pop,
            'Kode Jaringan: ' . $this->kode_jaringan,
        ]));
    }
}