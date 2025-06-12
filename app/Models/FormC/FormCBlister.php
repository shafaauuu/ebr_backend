<?php

namespace App\Models\FormC;

use App\Models\MasterBrm;
use App\Models\MasterMaterial;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormCBlister extends Model
{
    protected $table = 'form_c_blister';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'code_task',
        'id_brm',
        'batch_no',
        'actual_qty',
        'sesuai_picklist',
        'remarks_picklist',
        'sesuai_bets',
        'remarks_bets',
        'mat_lengkap',
        'remarks_mat',
        'task_id',
        'id_mat'
    ];

    protected $casts = [
        'sesuai_picklist' => 'boolean',
        'sesuai_bets' => 'boolean',
        'mat_lengkap' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(MasterMaterial::class, 'id_mat');
    }

    public function brm(): BelongsTo
    {
        return $this->belongsTo(MasterBrm::class, 'id_brm');
    }
}
