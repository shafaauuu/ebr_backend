<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayMachineShi2 extends Model
{
    protected $table = 'display_machine_shi_2';
    public $timestamps = true;
    public $incrementing = false; // No auto-incrementing primary key
    
    protected $fillable = [
        'brm_no',
        'material_type',
        'machine_id',
        'open_position_limit',
        'open_position_fourth',
        'open_position_third',
        'open_position_second',
        'open_position_first',
        'open_velocity_limit',
        'open_velocity_fourth',
        'open_velocity_third',
        'open_velocity_second',
        'open_velocity_first',
        'close_position_limit',
        'close_position_fourth',
        'close_position_third',
        'close_position_second',
        'close_position_first',
        'close_velocity_limit',
        'close_velocity_fourth',
        'close_velocity_third',
        'close_velocity_second',
        'close_velocity_first',
        'temperature_z1',
        'temperature_z2',
        'temperature_z3',
        'temperature_z4',
        'temperature_z5',
        'temperature_feeder',
        'filling_position_vp',
        'filling_position_first',
        'filling_velocity_vp',
        'filling_velocity_first',
        'filling_pressure',
        'holding_time_second',
        'holding_time_first',
        'holding_pressure_second',
        'holding_pressure_first',
        'holding_velocity',
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
        'plastictizing_cooling'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function formDDisplay(): BelongsTo
    {
        return $this->belongsTo(FormDDisplay::class, 'machine_id', 'machine_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(MasterMachine::class, 'machine_id', 'id_machine');
    }
}
