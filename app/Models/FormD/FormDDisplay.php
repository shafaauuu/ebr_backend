<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use App\Models\FormD\DisplayMachineAssy;
use App\Models\FormD\DisplayMachineFcs;
use App\Models\FormD\DisplayMachineFcsShi;
use App\Models\FormD\DisplayMachineShi1;
use App\Models\FormD\DisplayMachineShi2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormDDisplay extends Model
{
    protected $table = 'form_d_display';
    protected $primaryKey = 'id_form_d_display';
    public $timestamps = true;

    protected $fillable = [
        'machine_id',
        'material_type',
        'setting_march',
        'shift'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(MasterMachine::class, 'machine_id', 'id_machine');
    }
    
    public function displayMachineAssy(): HasOne
    {
        return $this->hasOne(DisplayMachineAssy::class, 'machine_id', 'machine_id');
    }
    
    public function displayMachineFcs(): HasOne
    {
        return $this->hasOne(DisplayMachineFcs::class, 'machine_id', 'machine_id');
    }
    
    public function displayMachineFcsShi(): HasOne
    {
        return $this->hasOne(DisplayMachineFcsShi::class, 'machine_id', 'machine_id');
    }
    
    public function displayMachineShi1(): HasOne
    {
        return $this->hasOne(DisplayMachineShi1::class, 'machine_id', 'machine_id');
    }
    
    public function displayMachineShi2(): HasOne
    {
        return $this->hasOne(DisplayMachineShi2::class, 'machine_id', 'machine_id');
    }
}
