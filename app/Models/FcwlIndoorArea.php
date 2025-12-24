<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FcwlIndoorArea extends Model
{
    protected $table = 'fcwl_indoor_area';
    protected $primaryKey = 'id_indoor';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcwl',
        'merk_ups',
        'kapasitas_ups',
        'jenis_ups',
        'ruangan_bebas_debu',
        'suhu_ruangan',
        'terpasang_ground_bar',
        'catuan_input_modem',
        'v_input_modem_p_n',
        'v_input_modem_n_g',
        'bertumpuk',
        'lokasi_ruang',
        'suhu_casing_modem',
        'catuan_input_terbounding',
        'splicing_konektor_kabel',
        'pemilik_perangkat_cpe',
        'jenis_perangkat_cpe',
    ];
    
    protected $casts = [
        'suhu_ruangan' => 'decimal:1',
        'v_input_modem_p_n' => 'decimal:2',
        'v_input_modem_n_g' => 'decimal:2',
        'suhu_casing_modem' => 'decimal:1',
    ];
    
    public function formChecklistWireless(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireless::class, 'id_fcwl', 'id_fcwl');
    }
    
    public function parameters(): HasMany
    {
        return $this->hasMany(FcwlIndoorParameter::class, 'id_indoor', 'id_indoor');
    }
    
    public function saranaPenunjangParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'sarana_penunjang');
    }
    
    public function perangkatModemParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'perangkat_modem');
    }
    
    public function perangkatCpeParameters(): HasMany
    {
        return $this->parameters()->where('kategori', 'perangkat_cpe');
    }
}
