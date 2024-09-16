<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['phone_number', 'password'];
    
    protected $hidden = ['password'];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }
}
