<?php

namespace App\Models\FormD;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineFcsShi extends Model
{
    protected $table = 'machine_fcs_shi';
    protected $primaryKey = 'id_machine_fsc_assy';
    public $timestamps = true;

    protected $fillable = [
        'code_task',
        'temp_nozzle_z1',
        'temp_nozzle_z2',
        'temp_nozzle_z3',
        'temp_nozzle_z4',
        'temp_nozzle_z5',
        'temp_mold',
        'inject_pressure',
        'inject_time',
        'holding_pressure',
        'holding_time',
        'eject_counter',
        'cycle_time',
        'masterbatch',
        'berat_produk',
        'berat_runner',
        'cavity',
        'sampling',
        'defect',
        'form_d_id',
        'task_id',
        'machine_picture'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
