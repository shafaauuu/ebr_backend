<?php

namespace App\Models\FormE;

use Illuminate\Database\Eloquent\Model;

class FormEInjection extends Model
{
    protected $table = 'form_e_injection';

    protected $fillable = [
        'code_task',
        'jml_teoritis',
        'jml_release',
        'jml_karantina',
        'jml_reject',
        'sample_ipc',
        'sample_qc',
        'sample_release',
        'yield',
        'total_hasil',
        'task_id',
    ];

    public $timestamps = true;

}
