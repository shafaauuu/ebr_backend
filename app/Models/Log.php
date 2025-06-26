<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'action',
        'created_by',
        'created_at',
        'updated_at',
        'code_task',
    ];

    /**
     * Get the task associated with this log
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
