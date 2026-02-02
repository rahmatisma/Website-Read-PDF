<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'equipment_type_id',
        'location_id',
        'brand',
        'model_type',
        'capacity',
        'reg_number',
        'serial_number',
        'kap_power_module',
        'type_pole',
        'height',
        'metadata',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function inspectionForms(): HasMany
    {
        return $this->hasMany(InspectionForm::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    // ==================== SCOPES ====================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeByType($query, string $typeCode)
    {
        return $query->whereHas('equipmentType', function ($q) use ($typeCode) {
            $q->where('type_code', $typeCode);
        });
    }
}