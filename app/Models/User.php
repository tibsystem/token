<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nome', 'email', 'senha_hash', 'tipo', 'telefone', 'status', 'status_kyc'
    ];

    public function wallet() {
        return $this->hasOne(Wallet::class);
    }

    public function kyc() {
        return $this->hasMany(Kyc::class);
    }

    public function investments() {
        return $this->hasMany(Investment::class);
    }
}

