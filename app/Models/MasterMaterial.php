<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMaterial extends Model
{
    use HasFactory;

    protected $table = 'master_materials';
    protected $primaryKey = 'id_mat';
    public $timestamps = false;

    protected $fillable = [
        'material_code',
        'material_desc',
        'material_type',
        'material_group',
        'material_uom',
        'created_at',
        'updated_at'
    ];
}
