<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPassword extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'password', 'changed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

