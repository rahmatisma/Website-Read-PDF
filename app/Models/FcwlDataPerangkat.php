<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_perangkat
 * @property int $id_fcwl
 * @property string $kategori
 * @property string $nama_barang
 * @property string|null $no_reg
 * @property string|null $serial_number
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireless $formChecklistWireless
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat cabut()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat existing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat penggantiPasangBaru()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat tidakTerpakai()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereIdPerangkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereNoReg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwlDataPerangkat whereSerialNumber($value)
 * @mixin \Eloquent
 */
class FcwlDataPerangkat extends Model
{
    protected $table = 'fcwl_data_perangkat';
    protected $primaryKey = 'id_perangkat';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'kategori',
        'nama_barang',
        'no_reg',
        'serial_number',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function scopeExisting($query)
    {
        return $query->where('kategori', 'existing');
    }
    
    public function scopeTidakTerpakai($query)
    {
        return $query->where('kategori', 'tidak_terpakai');
    }
    
    public function scopeCabut($query)
    {
        return $query->where('kategori', 'cabut');
    }
    
    public function scopePenggantiPasangBaru($query)
    {
        return $query->where('kategori', 'pengganti_pasang_baru');
    }
}
