<?php

namespace App\Models\FormF;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFBlister extends Model
{
    protected $table = 'form_f_blister';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'label_mesin',
        'label_2',
        'task_code',
        'task_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
