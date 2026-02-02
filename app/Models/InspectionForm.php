<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionForm extends Model
{
    protected $fillable = [
        'inspection_id',
        'form_id',
        'equipment_id',
        'instance_number',
        'status',
        'notes',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(FormsMaster::class, 'form_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(InspectionResult::class);
    }

    public function batteryBanks(): HasMany
    {
        return $this->hasMany(BatteryBankMetadata::class);
    }
}