<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormChecklistWireless extends Model
{
    protected $table = 'form_checklist_wireless';
    protected $primaryKey = 'id_fcwl';
    
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
    ];
    
    protected $casts = [
        'tanggal' => 'date',
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
        return $this->hasMany(FcwlWaktuPelaksanaan::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function tegangan(): HasMany
    {
        return $this->hasMany(FcwlTegangan::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function indoorArea(): HasOne
    {
        return $this->hasOne(FcwlIndoorArea::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function outdoorArea(): HasOne
    {
        return $this->hasOne(FcwlOutdoorArea::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function perangkatAntenna(): HasOne
    {
        return $this->hasOne(FcwlPerangkatAntenna::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function cablingInstallation(): HasOne
    {
        return $this->hasOne(FcwlCablingInstallation::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function dataPerangkat(): HasMany
    {
        return $this->hasMany(FcwlDataPerangkat::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function guidanceFoto(): HasMany
    {
        return $this->hasMany(FcwlGuidanceFoto::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function logs(): HasMany
    {
        return $this->hasMany(FcwlLog::class, 'id_fcwl', 'id_fcwl');
    }
}