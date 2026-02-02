<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'location_name',
        'room',
        'address',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class);
    }
}