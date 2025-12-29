<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id_fcw
 * @property int $id_spk
 * @property string $no_spk
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string|null $nama_pelanggan
 * @property string|null $contact_person
 * @property string|null $nomor_telepon
 * @property string|null $alamat
 * @property string|null $kota
 * @property string|null $propinsi
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string|null $posisi_modem_di_lt
 * @property string|null $ruang
 * @property string|null $grounding_bar_terkoneksi_ke
 * @property string|null $ac_pendingin_ruangan
 * @property numeric|null $suhu_ruangan_perangkat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwChecklistItem> $checklistItems
 * @property-read int|null $checklist_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwDataPerangkat> $dataPerangkat
 * @property-read int|null $data_perangkat_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwGuidanceFoto> $guidanceFoto
 * @property-read int|null $guidance_foto_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\SPK $spk
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwTegangan> $tegangan
 * @property-read int|null $tegangan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcwWaktuPelaksanaan> $waktuPelaksanaan
 * @property-read int|null $waktu_pelaksanaan_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereAcPendinginRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereGroundingBarTerkoneksiKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereIdFcw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereIdSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereKota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereNamaPelanggan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereNoSpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereNomorTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline wherePosisiModemDiLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline wherePropinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereRuang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereSuhuRuanganPerangkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormChecklistWireline whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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