<?php

namespace App\Models\FormD;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineSgp extends Model
{
    protected $table = 'machine_sgp';
    protected $primaryKey = 'id_machine_fcs';
    public $timestamps = false;

    protected $fillable = [
        'code_task',
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
        'hub_cannula1',
        'hub_cannula2',
        'hub_cannula3',
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
        'needle_tersumbat3',
        'masterbatch',
        'berat_product',
        'cavity',
        'berat_produk',
        'sampling',
        'defect',
        'approval_hasil_printing',
        'form_d_id',
        'task_id',
        'machine_picture'
    ];

    protected $casts = [
        'load_cap' => 'boolean',
        'load_hub' => 'boolean',
        'load_needle' => 'boolean',
        'hasil_epoxy' => 'boolean',
        'pressure_status' => 'boolean',
        'low_epoxy1' => 'boolean',
        'low_epoxy2' => 'boolean',
        'low_epoxy3' => 'boolean',
        'hub_cannula1' => 'boolean',
        'hub_cannula2' => 'boolean',
        'hub_cannula3' => 'boolean',
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
        'needle_tersumbat3' => 'boolean',
        'temp1' => 'float',
        'temp2' => 'float',
        'temp3' => 'float',
        'temp4' => 'float',
        'pressure_actual' => 'float',
        'berat_product' => 'float',
        'berat_produk' => 'float',
        'masterbatch' => 'integer',
        'cavity' => 'integer',
        'sampling' => 'integer',
        'defect' => 'integer',
        'approval_hasil_printing' => 'binary',
        'machine_picture' => 'binary'
    ];

    /**
     * Get the FormD that owns the machine SGP data.
     */
    public function formD(): BelongsTo
    {
        return $this->belongsTo(FormD::class, 'form_d_id', 'id_form_d');
    }

    /**
     * Get the Task that owns the machine SGP data.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
