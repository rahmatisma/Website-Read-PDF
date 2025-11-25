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
}