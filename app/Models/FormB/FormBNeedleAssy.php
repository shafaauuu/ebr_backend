<?php

namespace App\Models\FormB;

use Illuminate\Database\Eloquent\Model;

class FormBNeedleAssy extends Model
{
    protected $table = 'form_b_needle_assy';
    protected $fillable = ['code_task', 'machine_id', 'terkualifikasi', 'task_id'];
}
