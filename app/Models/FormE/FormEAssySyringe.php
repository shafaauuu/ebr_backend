<?php

namespace App\Models\FormE;

use Illuminate\Database\Eloquent\Model;

class FormEAssySyringe extends Model
{
    protected $table = 'form_e_assy_syringe';

    protected $fillable = [
        'code_task',
        'jml_teoritis',
        'jml_release',
        'jml_karantina',
        'jml_reject',
        'jml_sisa',
        'sample_ipc',
        'sample_qc',
        'sample_release',
        'yield',
        'total_hasil',
        'task_id',
    ];

    public $timestamps = true;

}
