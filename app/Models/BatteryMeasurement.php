<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatteryMeasurement extends Model
{
    protected $fillable = [
        'battery_bank_id',
        'cell_number',
        'voltage',
        'soh',
    ];

    protected $casts = [
        'voltage' => 'decimal:2',
        'soh' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function batteryBank(): BelongsTo
    {
        return $this->belongsTo(BatteryBankMetadata::class, 'battery_bank_id');
    }

    // ==================== SCOPES ====================
    
    public function scopeLowSoh($query, int $threshold = 80)
    {
        return $query->where('soh', '<', $threshold);
    }

    public function scopeLowVoltage($query, float $threshold = 12.0)
    {
        return $query->where('voltage', '<', $threshold);
    }
}