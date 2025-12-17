<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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