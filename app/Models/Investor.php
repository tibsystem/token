<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Investor extends Authenticatable implements JWTSubject
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nome',
        'email',
        'documento',
        'telefone',
        'senha_hash',
        'status_kyc',
        'carteira_blockchain',
    ];

    protected $hidden = [
        'senha_hash',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class, 'id_investidor');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Override the default password field for authentication.
     */
    public function getAuthPassword()
    {
        return $this->senha_hash;
    }
}
