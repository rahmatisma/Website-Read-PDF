<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_perangkat
 * @property int $id_fcw
 * @property string $kategori
 * @property string $nama_barang
 * @property string|null $no_reg
 * @property string|null $serial_number
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\FormChecklistWireline $formChecklistWireline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat cabut()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat existing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat penggantiPasangBaru()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat tidakTerpakai()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereIdPerangkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereNoReg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcwDataPerangkat whereSerialNumber($value)
 * @mixin \Eloquent
 */
class FcwDataPerangkat extends Model
{
    protected $table = 'fcw_data_perangkat';
    protected $primaryKey = 'id_perangkat';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'kategori',
        'nama_barang',
        'no_reg',
        'serial_number',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
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
