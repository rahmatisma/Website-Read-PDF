<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
