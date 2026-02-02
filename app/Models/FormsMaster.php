<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormsMaster extends Model
{
    protected $table = 'forms_master';

    protected $fillable = [
        'form_code',
        'form_title',
        'form_type',
        'equipment_type_id',
        'version',
        'page_total',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(FormsChecklistMaster::class, 'form_id');
    }

    public function inspectionForms(): HasMany
    {
        return $this->hasMany(InspectionForm::class, 'form_id');
    }

    // ==================== SCOPES ====================
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $formType)
    {
        return $query->where('form_type', $formType);
    }
}