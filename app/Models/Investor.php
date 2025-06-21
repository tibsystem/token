<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Investor extends Authenticatable
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
}
