<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMachine extends Model
{
    use HasFactory;

    protected $table = 'master_machines';
    protected $primaryKey = 'id_machine'; // Adjust if needed
    public $timestamps = true; // Set to false if your table doesn't have created_at / updated_at

    protected $fillable = [
        'machine_code',
        'machine_name',
        // Add other fields if present in your table
    ];

}
