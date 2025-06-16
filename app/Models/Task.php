<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $fillable = ['status', 'assigned_by', 'assigned_to', 'id_brm', 'no_batch'];

    public function masterBrm()
    {
        return $this->belongsTo(MasterBrm::class, 'id_brm', 'id_brm');
    }


    public function assignedBy() {
        return $this->belongsTo(User::class, 'assigned_by', 'nik');
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to', 'nik');
    }
}
