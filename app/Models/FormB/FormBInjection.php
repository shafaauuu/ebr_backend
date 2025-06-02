<?php

namespace App\Models\FormB;

use Illuminate\Database\Eloquent\Model;

class FormBInjection extends Model
{
    protected $table = 'form_b_injection';
    protected $fillable = ['code_task', 'machine_id', 'terkualifikasi', 'task_id'];
}
