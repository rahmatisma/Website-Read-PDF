<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcwTegangan extends Model
{
    protected $table = 'fcw_tegangan';
    protected $primaryKey = 'id_tegangan';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_fcw',
        'jenis_sumber',
        'p_n',
        'p_g',
        'n_g',
    ];
    
    protected $casts = [
        'p_n' => 'decimal:2',
        'p_g' => 'decimal:2',
        'n_g' => 'decimal:2',
    ];
    
    public function formChecklistWireline(): BelongsTo
    {
        return $this->belongsTo(FormChecklistWireline::class, 'id_fcw', 'id_fcw');
    }
}