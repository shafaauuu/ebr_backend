<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBrm extends Model
{
    use HasFactory;

    protected $table = 'master_brms';
    protected $primaryKey = 'id_brm';
    public $timestamps = true;

    protected $fillable = [
        'material_code',
        'brm_no',
        'brm_machine',
        'product_code',
        'product_name',
    ];

}
