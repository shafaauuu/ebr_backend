<?php

namespace App\Models\FormE;

use Illuminate\Database\Eloquent\Model;

class FormEBlister extends Model
{
    protected $table = 'form_e_blister';

    protected $fillable = [
        'code_task',
        'jml_awal_assy',
        'jml_karantina_assy',
        'total_syringe',
        'jml_fg',
        'jml_karantina',
        'jml_reject',
        'jml_sisa',
        'sample_ipc',
        'sample_qc',
        'sample_released',
        'yield',
        'total_prod',
        'task_id',
    ];

    public $timestamps = true;
}
