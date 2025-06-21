<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarteiraInterna extends Model
{
    use HasFactory;

    protected $table = 'carteiras_internas';

    protected $fillable = [
        'id_investidor',
        'endereco_wallet',
        'saldo_disponivel',
        'saldo_bloqueado',
        'saldo_tokenizado',
    ];

    protected $casts = [
        'saldo_tokenizado' => 'array',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'id_investidor');
    }
}
