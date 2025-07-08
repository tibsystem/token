<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject

{
    use HasFactory, Notifiable, HasRoles;


    protected $fillable = [
        'nome',
        'email',
        'password',
        'tipo',
        'telefone',
        'status',
        'status_kyc',
    ];
    // Obrigatórios para JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function kyc()
    {
        return $this->hasMany(Kyc::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    // Participante vinculado (se este user for um participante)
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    // Participantes da empresa (se este user for uma empresa)
    public function participants()
    {
        return $this->hasMany(Participant::class, 'investor_id');
    }
}
