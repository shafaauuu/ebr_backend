<?php

namespace App\Models\FormG;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormGAssySyringe extends Model
{
    protected $table = 'form_g_assy_syringe';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'remarks',
        'signed_1',
        'inisial_1',
        'signed_2',
        'inisial_2',
        'signed_3',
        'inisial_3',
        'task_code',
        'task_id'
    ];

    // Exclude binary fields from JSON serialization
    protected $hidden = [
        'signed_1',
        'signed_2',
        'signed_3',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Add accessors for signature existence
    protected $appends = [
        'has_signed_1',
        'has_signed_2',
        'has_signed_3',
    ];

    public function getHasSigned1Attribute()
    {
        return !empty($this->signed_1);
    }

    public function getHasSigned2Attribute()
    {
        return !empty($this->signed_2);
    }

    public function getHasSigned3Attribute()
    {
        return !empty($this->signed_3);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
