<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends Model
{
    protected $fillable = [
        'type_code',
        'type_name',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function formsMaster(): HasMany
    {
        return $this->hasMany(FormsMaster::class);
    }
}