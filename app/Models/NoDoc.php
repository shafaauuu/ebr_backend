<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoDoc extends Model
{
    use HasFactory;

    protected $table = 'no_doc'; // Table name

    protected $primaryKey = 'id'; // Primary key

    public $timestamps = true; // Enables created_at and updated_at

    protected $fillable = [
        'no_batch',
        'code_task',
        'status',
        'assigned_by',
        'assigned_to',
        'created_at',
        'updated_at',
    ];

    // Optional: Relationship to logs
    public function logs()
    {
        return $this->hasMany(Log::class, 'code_task', 'code_task');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'nik');
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'nik');
    }

}
