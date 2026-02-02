<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionResult extends Model
{
    protected $fillable = [
        'inspection_form_id',
        'checklist_item_id',
        'result_value',
        'status',
        'comment',
        'measurement_label',
        'row_number',
        'column_name',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function inspectionForm(): BelongsTo
    {
        return $this->belongsTo(InspectionForm::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(FormsChecklistMaster::class, 'checklist_item_id');
    }

    // ==================== SCOPES ====================
    
    public function scopeOk($query)
    {
        return $query->where('status', 'OK');
    }

    public function scopeNok($query)
    {
        return $query->where('status', 'NOK');
    }

    public function scopeByMeasurementLabel($query, string $label)
    {
        return $query->where('measurement_label', $label);
    }
}