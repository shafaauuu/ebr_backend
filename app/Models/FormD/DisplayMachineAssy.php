<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayMachineAssy extends Model
{
    protected $table = 'display_machine_assy';
    public $timestamps = true;
    public $incrementing = false; // No auto-incrementing primary key
    
    protected $fillable = [
        'brm_no',
        'machine_id',
        'material',
        'print_mach_speed',
        'assy_mach_speed',
        'silicon_spray'
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
