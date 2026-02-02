<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelaksana extends Model
{
    protected $table = 'pelaksana';

    protected $fillable = [
        'nama',
        'department',
        'sub_department',
        'perusahaan',
        'nik',
        'mitra_internal',
    ];

    // ==================== RELATIONSHIPS ====================
    
    // Inspections - as executor 1
    public function inspectionsAsExecutor1(): HasMany
    {
        return $this->hasMany(Inspection::class, 'pelaksana1_id');
    }

    // Inspections - as executor 2
    public function inspectionsAsExecutor2(): HasMany
    {
        return $this->hasMany(Inspection::class, 'pelaksana2_id');
    }

    // Inspections - as executor 3
    public function inspectionsAsExecutor3(): HasMany
    {
        return $this->hasMany(Inspection::class, 'pelaksana3_id');
    }

    // Inspections - as executor 4
    public function inspectionsAsExecutor4(): HasMany
    {
        return $this->hasMany(Inspection::class, 'pelaksana4_id');
    }

    // Inspections - as verifier
    public function inspectionsAsVerifier(): HasMany
    {
        return $this->hasMany(Inspection::class, 'verified_by_id');
    }

    // Inspections - as approver
    public function inspectionsAsApprover(): HasMany
    {
        return $this->hasMany(Inspection::class, 'approved_by_id');
    }

    // Equipment Inventories
    public function inventoriesAsExecutor1(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class, 'pelaksana1_id');
    }

    public function inventoriesAsVerifier(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class, 'verified_by_id');
    }

    public function inventoriesAsApprover(): HasMany
    {
        return $this->hasMany(EquipmentInventory::class, 'approved_by_id');
    }
}