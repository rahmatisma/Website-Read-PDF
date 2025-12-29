<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id_fcwl
 * @property int $id_spk
 * @property string $no_spk
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string|null $nama_pelanggan
 * @property string|null $contact_person
 * @property string|null $nomor_telepon
 * @property string|null $alamat
 * @property string|null $kota
 * @property string|null $propinsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FcwlCablingInstallation|null $cablingInstallation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlDataPerangkat> $dataPerangkat
 * @property-read int|null $data_perangkat_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlGuidanceFoto> $guidanceFoto
 * @property-read int|null $guidance_foto_count
 * @property-read \App\Models\FcwlIndoorArea|null $indoorArea
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\FcwlOutdoorArea|null $outdoorArea
 * @property-read \App\Models\FcwlPerangkatAntenna|null $perangkatAntenna
 * @property-read \App\Models\SPK $spk
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlTegangan> $tegangan
 * @property-read int|null $tegangan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwlWaktuPelaksanaan> $waktuPelaksanaan
 * @property-read int|null $waktu_pelaksanaan_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereIdFcwl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereKota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereNamaPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereNoSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereNomorTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless wherePropinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireless whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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