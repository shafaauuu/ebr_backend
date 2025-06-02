<?php

namespace App\Models\FormE;

use Illuminate\Database\Eloquent\Model;

class FormENeedleAssy extends Model
{
    protected $table = 'form_e_needle_assy';

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
