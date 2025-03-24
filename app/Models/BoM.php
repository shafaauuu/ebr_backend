<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoM extends Model
{
    use HasFactory;

    protected $table = 'boms';
    protected $primaryKey = 'id_bom';
    public $timestamps = false;

    protected $fillable = [
        'id_bom',
        'id_mat',
        'material_code',
        'bom_level',
        'child_mat',
    ];
}
