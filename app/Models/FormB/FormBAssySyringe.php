<?php

namespace App\Models\FormB;

use Illuminate\Database\Eloquent\Model;

class FormBAssySyringe extends Model
{
    protected $table = 'form_b_assy_syringe';
    protected $fillable = ['code_task', 'machine_id', 'terkualifikasi', 'task_id'];

}
