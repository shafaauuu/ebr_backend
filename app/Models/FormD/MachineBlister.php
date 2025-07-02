<?php

namespace App\Models\FormD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FormD;
use App\Models\Task;

class MachineBlister extends Model
{
    use HasFactory;

    protected $table = 'machine_blister';
    protected $primaryKey = 'id_machine_blister';
    public $timestamps = false;

    protected $fillable = [
        'code_task',
        'forming_time',
        'forming_temperature',
        'forming_pressure',
        'sealing_temperature',
        'sealing_pressure',
        'sealing_time',
        'cycle_time',
        'mfg_date',
        'exp_date',
        'needle_size',
        'nie',
        'approval_hasil_printing',
        'form_d_id',
        'task_id',
        'machine_picture'
    ];

    /**
     * Get the FormD that owns the machine blister data.
     */
    public function formD()
    {
        return $this->belongsTo(FormD::class, 'form_d_id', 'id_form_d');
    }

    /**
     * Get the Task that owns the machine blister data.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
