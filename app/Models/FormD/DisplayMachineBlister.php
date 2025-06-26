<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayMachineBlister extends Model
{
    protected $table = 'display_machine_blister';
    public $timestamps = true;
    public $incrementing = false; // No auto-incrementing primary key
    
    protected $fillable = [
        'brm_no',
        'machine_id',
        'material_type',
        'forming_time',
        'forming_temperature',
        'forming_pressure',
        'sealing_temperature',
        'sealing_pressure',
        'sealing_time'
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
