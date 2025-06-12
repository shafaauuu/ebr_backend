<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bom extends Model
{
    use HasFactory;

    protected $table = 'boms';
    protected $primaryKey = 'id_bom';
    public $timestamps = true;

    protected $fillable = [
        'id_mat',
        'material_code',
        'bom_level',
        'child_mat',
        'fact_index',
        'child_mat_id'
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'id_mat');
    }

    public function childMaterial(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'child_mat_id');
    }
}
