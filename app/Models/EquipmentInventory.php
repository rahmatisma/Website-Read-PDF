<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentInventory extends Model
{
    protected $table = 'equipment_inventory';

    protected $fillable = [
        'form_code',
        'inspection_id',
        'location_id',
        'inventory_date',
        'inventory_time',
        'pelaksana1_id',
        'pelaksana2_id',
        'pelaksana3_id',
        'pelaksana4_id',
        'verified_by_id',
        'approved_by_id',
        'notes',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'inventory_time' => 'datetime:H:i',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function pelaksana1(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'pelaksana1_id');
    }

    public function pelaksana2(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'pelaksana2_id');
    }

    public function pelaksana3(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'pelaksana3_id');
    }

    public function pelaksana4(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'pelaksana4_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'verified_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Pelaksana::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'inventory_id');
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Get items by section
     */
    public function getItemsBySection(string $sectionName)
    {
        return $this->items()->where('section_name', $sectionName)->get();
    }

    /**
     * Get total equipment count
     */
    public function getTotalEquipmentCount(): int
    {
        return $this->items()->sum('quantity');
    }
}