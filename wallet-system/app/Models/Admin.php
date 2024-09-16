<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable

{
    use HasApiTokens;
    protected $fillable = ['phone_number', 'password'];
    
    protected $hidden = ['password'];
}

