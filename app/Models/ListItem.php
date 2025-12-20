<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'list_item';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_item';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_spk',
        'kode',
        'deskripsi',
        'serial_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the SPK that owns the list item.
     * 
     * Relationship: MANY-TO-ONE (belongsTo)
     * Multiple items can belong to one SPK
     */
    public function spk()
    {
        return $this->belongsTo(Spk::class, 'id_spk', 'id_spk');
    }

    /**
     * Get the dokumentasi foto for this list item.
     * 
     * Relationship: ONE-TO-MANY (hasMany)
     * One item can have multiple photos with kategori 'list_item'
     */
    public function dokumentasiFoto()
    {
        return $this->hasMany(DokumentasiFoto::class, 'id_spk', 'id_spk')
                    ->where('kategori_foto', 'list_item');
    }
}