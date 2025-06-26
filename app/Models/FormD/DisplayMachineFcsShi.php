<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayMachineFcsShi extends Model
{
    protected $table = 'display_machine_fcs_shi';
    public $timestamps = true;
    public $incrementing = false; // No auto-incrementing primary key
    
    protected $fillable = [
        'brm_no',
        'material_type',
        'machine_id',
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
        'cycle_time'
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
