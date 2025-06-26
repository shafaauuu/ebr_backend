<?php

namespace App\Models\FormD;

use App\Models\Bom;
use App\Models\MasterMaterial;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRecon extends Model
{
    protected $table = 'material_recon';
    protected $primaryKey = 'id_mat_recon';
    public $timestamps = true;

    protected $fillable = [
        'material_code',
        'material_uom',
        'jml_awal',
        'jml_spbt',
        'jml_reject',
        'jml_pakai',
        'jml_karantina',
        'sisa',
        'jml_musnah',
        'jml_kembali',
        'code_task',
        'id_task',
        'id_mat',
        'id_bom'
    ];

    protected $casts = [
        'jml_awal' => 'integer',
        'jml_spbt' => 'integer',
        'jml_reject' => 'integer',
        'jml_pakai' => 'integer',
        'jml_karantina' => 'integer',
        'sisa' => 'integer',
        'jml_musnah' => 'integer',
        'jml_kembali' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'id_task');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'id_mat');
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class, 'id_bom');
    }
}
