<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatteryBankMetadata extends Model
{
    protected $table = 'battery_bank_metadata';

    protected $fillable = [
        'inspection_form_id',
        'bank_number',
        'bank_name',
        'battery_type',
        'battery_brand',
        'battery_capacity',
        'production_date',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function inspectionForm(): BelongsTo
    {
        return $this->belongsTo(InspectionForm::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(BatteryMeasurement::class, 'battery_bank_id');
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Get average SOH for this battery bank
     */
    public function getAverageSoh(): ?float
    {
        return $this->measurements()
            ->whereNotNull('soh')
            ->avg('soh');
    }

    /**
     * Get minimum voltage in this bank
     */
    public function getMinVoltage(): ?float
    {
        return $this->measurements()
            ->whereNotNull('voltage')
            ->min('voltage');
    }

    /**
     * Get cells below SOH threshold
     */
    public function getLowSohCells(int $threshold = 80)
    {
        return $this->measurements()
            ->where('soh', '<', $threshold)
            ->get();
    }
}