<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_role';
    protected $fillable = ['user_nik', 'role_auth_id'];
    public $timestamps = true;

    public function roleAuth()
    {
        return $this->belongsTo(RoleAuth::class, 'role_auth_id', 'role_auth_id');
    }
}
