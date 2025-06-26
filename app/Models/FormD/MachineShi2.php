<?php

namespace App\Models\FormD;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineShi2 extends Model
{
    protected $table = 'machine_shi_2';
    protected $primaryKey = 'id_machine_shi_2';
    public $timestamps = true;

    protected $fillable = [
        'code_task',
        'open_position_openlimit',
        'open_position_forth',
        'open_position_third',
        'open_position_second',
        'open_position_first',
        'open_velocity_limit',
        'open_velocity_forth',
        'open_velocity_third',
        'open_velocity_second',
        'open_velocity_first',
        'close_position_limit',
        'close_position_forth',
        'close_position_third',
        'close_position_second',
        'close_position_first',
        'close_velocity_limit',
        'close_velocity_forth',
        'close_velocity_third',
        'close_velocity_second',
        'close_velocity_first',
        'temperature_z1',
        'temperature_z2',
        'temperature_z3',
        'temperature_z4',
        'temperature_z5',
        'filling_position_vp',
        'filling_position_first',
        'filling_velocity_vp',
        'filling_velocity_first',
        'holding_time_second',
        'holding_time_first',
        'holding_pressure_second',
        'holding_pressure_first',
        'plastictizing_pullback_position',
        'plastictizing_pullback_velocity',
        'plastictizing_dose1_position',
        'plastictizing_dose1_backpress',
        'plastictizing_dose1_rotation',
        'plastictizing_end_position',
        'plastictizing_end_backpress',
        'plastictizing_end_rotation',
        'plastictizing_forward_position',
        'plastictizing_forward_velocity',
        'plastictizing_cooling',
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
