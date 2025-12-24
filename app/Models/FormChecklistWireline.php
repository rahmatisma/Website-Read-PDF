<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormChecklistWireline extends Model
{
    protected $table = 'form_checklist_wireline';
    protected $primaryKey = 'id_fcw';
    
    protected $fillable = [
        'id_spk',
        'no_spk',
        'tanggal',
        'nama_pelanggan',
        'contact_person',
        'nomor_telepon',
        'alamat',
        'kota',
        'propinsi',
        'latitude',
        'longitude',
        'posisi_modem_di_lt',
        'ruang',
        'grounding_bar_terkoneksi_ke',
        'ac_pendingin_ruangan',
        'suhu_ruangan_perangkat',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'suhu_ruangan_perangkat' => 'decimal:1',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function spk(): BelongsTo
    {
        return $this->belongsTo(Spk::class, 'id_spk', 'id_spk');
    }
    
    public function waktuPelaksanaan(): HasMany
    {
        return $this->hasMany(FcwWaktuPelaksanaan::class, 'id_fcw', 'id_fcw');
    }
    
    public function tegangan(): HasMany
    {
        return $this->hasMany(FcwTegangan::class, 'id_fcw', 'id_fcw');
    }
    
    public function checklistItems(): HasMany
    {
        return $this->hasMany(FcwChecklistItem::class, 'id_fcw', 'id_fcw');
    }
    
    public function dataPerangkat(): HasMany
    {
        return $this->hasMany(FcwDataPerangkat::class, 'id_fcw', 'id_fcw');
    }
    
    public function guidanceFoto(): HasMany
    {
        return $this->hasMany(FcwGuidanceFoto::class, 'id_fcw', 'id_fcw');
    }
    
    public function logs(): HasMany
    {
        return $this->hasMany(FcwLog::class, 'id_fcw', 'id_fcw');
    }
}