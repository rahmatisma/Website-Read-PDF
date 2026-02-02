<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormsChecklistMaster extends Model
{
    protected $table = 'forms_checklist_master';

    protected $fillable = [
        'form_id',
        'parent_section_id',
        'section_number',
        'section_name',
        'item_code',
        'item_description',
        'operational_standard',
        'item_order',
        'has_multiple_measurements',
        'measurement_labels',
        'is_conditional',
        'conditional_field',
        'conditional_standards',
    ];

    protected $casts = [
        'has_multiple_measurements' => 'boolean',
        'is_conditional' => 'boolean',
        'conditional_standards' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function form(): BelongsTo
    {
        return $this->belongsTo(FormsMaster::class, 'form_id');
    }

    public function parentSection(): BelongsTo
    {
        return $this->belongsTo(FormsChecklistMaster::class, 'parent_section_id');
    }

    public function childSections(): HasMany
    {
        return $this->hasMany(FormsChecklistMaster::class, 'parent_section_id');
    }

    public function inspectionResults(): HasMany
    {
        return $this->hasMany(InspectionResult::class, 'checklist_item_id');
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Get measurement labels as array
     */
    public function getMeasurementLabelsArray(): array
    {
        if (empty($this->measurement_labels)) {
            return [];
        }

        return array_map('trim', explode(',', $this->measurement_labels));
    }

    /**
     * Get operational standard based on equipment property
     */
    public function getConditionalStandard(?string $equipmentValue): ?string
    {
        if (!$this->is_conditional || empty($this->conditional_standards)) {
            return $this->operational_standard;
        }

        return $this->conditional_standards[$equipmentValue] ?? $this->operational_standard;
    }
}