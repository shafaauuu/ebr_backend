<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name', 'last_name', 'email',
        'nik', 'divisi', 'department', 'role_id', 'preregistered_at'
    ];

    protected $primaryKey = 'nik';
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
        return $this->hasOneThrough(RoleAuth::class, UserRole::class, 'user_nik', 'id', 'nik', 'role_id');
    }

    public function passwords()
    {
        return $this->hasMany(UserPassword::class);
    }
}
