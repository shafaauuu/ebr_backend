<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $fillable = ['code', 'task_name', 'status', 'assigned_by', 'assigned_to'];

    public function assignedBy() {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
