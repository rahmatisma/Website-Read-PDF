<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Inspection extends Model
{
    protected $fillable = [
        'inspection_code',
        'location_id',
        'inspection_date',
        'inspection_time',
        'status',
        'notes',
        'pelaksana1_id',
        'pelaksana2_id',
        'pelaksana3_id',
        'pelaksana4_id',
        'verified_by_id',
        'approved_by_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'inspection_time' => 'datetime:H:i',
    ];

    // ==================== RELATIONSHIPS ====================
    
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

    public function inspectionForms(): HasMany
    {
        return $this->hasMany(InspectionForm::class);
    }

    public function equipmentInventories(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class);
    }

    // ==================== SCOPES ====================
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function scopeByLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('inspection_date', [$startDate, $endDate]);
    }
}