<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'nik';

    protected $fillable = [
        'nik',
        'first_name',
        'last_name',
        'email',
        'password',
        'position',
        'div',
        'dept',
        'created_at',
        'updated_at',
        'inisial',
        'group',
    ];

    public $incrementing = false; // Because nik is a string
    protected $keyType = 'string';

    // Define relationship to user_role table
    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'user_nik', 'nik');
    }

    // Define relationship to role_auth through user_role
    public function role()
    {
        return $this->hasOneThrough(RoleAuth::class, UserRole::class, 'user_nik', 'role_auth_id', 'nik', 'role_auth_id');
    }

    public function passwords()
    {
        return $this->hasMany(UserPassword::class);
    }

    public function tasksAssignedBy()
    {
        return $this->hasMany(NoDoc::class, 'assigned_by', 'nik');
    }

    public function tasksAssignedTo()
    {
        return $this->hasMany(NoDoc::class, 'assigned_to', 'nik');
    }

}
