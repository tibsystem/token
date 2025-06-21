<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_investidor',
        'id_imovel',
        'qtd_tokens',
        'valor_unitario',
        'data_compra',
        'origem',
        'status',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class, 'id_investidor');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'id_imovel');
    }
}
