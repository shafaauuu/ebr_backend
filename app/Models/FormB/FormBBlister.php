<?php

namespace App\Models\FormB;

use Illuminate\Database\Eloquent\Model;

class FormBBlister extends Model
{
    protected $table = 'form_b_blister';
    protected $fillable = ['code_task', 'machine_id', 'terkualifikasi', 'task_id'];
}
