<?php

namespace App\Models\FormD;

use App\Models\FormDDisplay;
use App\Models\MachineAssy;
use App\Models\MachineFcs;
use App\Models\MachineFcsShi;
use App\Models\MachineShi1;
use App\Models\MachineShi2;
use App\Models\MasterMachine;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormD extends Model
{
    protected $table = 'form_d';
    protected $primaryKey = 'id_form_d';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'machine_id',
        'material_type',
        'code_task',
        'brm_no',
        'shift',
        'form_d_id',
        'task_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(MasterMachine::class, 'machine_id', 'machine_code');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function formDDisplay(): BelongsTo
    {
        return $this->belongsTo(FormDDisplay::class, 'form_d_id', 'id_form_d_display');
    }

    public function machineAssy(): HasOne
    {
        return $this->hasOne(MachineAssy::class, 'form_d_id', 'id_form_d');
    }

    public function machineFcs(): HasOne
    {
        return $this->hasOne(MachineFcs::class, 'form_d_id', 'id_form_d');
    }

    public function machineFcsShi(): HasOne
    {
        return $this->hasOne(MachineFcsShi::class, 'form_d_id', 'id_form_d');
    }

    public function machineShi1(): HasOne
    {
        return $this->hasOne(MachineShi1::class, 'form_d_id', 'id_form_d');
    }

    public function machineShi2(): HasOne
    {
        return $this->hasOne(MachineShi2::class, 'form_d_id', 'id_form_d');
    }
}
