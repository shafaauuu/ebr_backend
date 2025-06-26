<?php

namespace App\Models\FormD;

use App\Models\MasterMachine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayMachineSgp extends Model
{
    protected $table = 'display_machine_sgp';
    public $timestamps = true;
    public $incrementing = false; // No auto-incrementing primary key
    
    protected $fillable = [
        'brm_no',
        'machine_id',
        'material_type',
        'temp1',
        'temp2',
        'temp3',
        'temp4',
        'load_cap',
        'load_hub',
        'load_needle',
        'hasil_epoxy',
        'pressure_actual',
        'pressure_status',
        'low_epoxy1',
        'low_epoxy2',
        'low_epoxy3',
        'hub_canula1',
        'hub_canula2',
        'hub_canula3',
        'exc_epoxy1',
        'exc_epoxy2',
        'exc_epoxy3',
        'needle_tumpul1',
        'needle_tumpul2',
        'needle_tumpul3',
        'needle_balik1',
        'needle_balik2',
        'needle_balik3',
        'needle_tersumbat1',
        'needle_tersumbat2',
        'needle_tersumbat3'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'load_cap' => 'boolean',
        'load_hub' => 'boolean',
        'load_needle' => 'boolean',
        'hasil_epoxy' => 'boolean',
        'pressure_status' => 'boolean',
        'low_epoxy1' => 'boolean',
        'low_epoxy2' => 'boolean',
        'low_epoxy3' => 'boolean',
        'hub_canula1' => 'boolean',
        'hub_canula2' => 'boolean',
        'hub_canula3' => 'boolean',
        'exc_epoxy1' => 'boolean',
        'exc_epoxy2' => 'boolean',
        'exc_epoxy3' => 'boolean',
        'needle_tumpul1' => 'boolean',
        'needle_tumpul2' => 'boolean',
        'needle_tumpul3' => 'boolean',
        'needle_balik1' => 'boolean',
        'needle_balik2' => 'boolean',
        'needle_balik3' => 'boolean',
        'needle_tersumbat1' => 'boolean',
        'needle_tersumbat2' => 'boolean',
        'needle_tersumbat3' => 'boolean'
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
