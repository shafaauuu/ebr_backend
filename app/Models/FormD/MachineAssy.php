<?php

namespace App\Models\FormD;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineAssy extends Model
{
    protected $table = 'machine_assy';
    protected $primaryKey = 'id_machine_assy';
    public $timestamps = false;

    protected $fillable = [
        'code_task',
        'print_match_speed',
        'assy_match_speed',
        'approval',
        'load_barrel',
        'load_plunger',
        'load_gasket',
        'actual_running',
        'run_awal',
        'defect',
        'goods_ok',
        'goods_reject',
        'qc_barrel',
        'qc_gasket',
        'qc_plunger',
        'form_d_id',
        'task_id',
        'machine_picture'
    ];

    protected $casts = [
        'load_barrel' => 'boolean',
        'load_plunger' => 'boolean',
        'load_gasket' => 'boolean',
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
    ];

    public function formD(): BelongsTo
    {
        return $this->belongsTo(FormD::class, 'form_d_id', 'id_form_d');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
