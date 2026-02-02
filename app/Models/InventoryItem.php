<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_id',
        'row_number',
        'section_name',
        'equipment_name',
        'equipment_id',
        'quantity',
        'status',
        'bonding_ground',
        'remarks',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(EquipmentInventory::class, 'inventory_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // ==================== SCOPES ====================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeShutdown($query)
    {
        return $query->where('status', 'SHUTDOWN');
    }

    public function scopeBySection($query, string $sectionName)
    {
        return $query->where('section_name', $sectionName);
    }

    public function scopeConnectedToGround($query)
    {
        return $query->where('bonding_ground', 'CONNECT');
    }
}