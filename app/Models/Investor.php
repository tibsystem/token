<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'documento',
        'telefone',
        'status_kyc',
        'carteira_blockchain',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class, 'id_investidor');
    }
}
