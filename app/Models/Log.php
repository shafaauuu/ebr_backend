<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'no_doc',
        'action',
        'created_date',
        'created_by',
    ];
}
