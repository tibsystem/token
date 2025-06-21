<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2PListing extends Model
{
    use HasFactory;

    protected $table = 'p2p_listings';

    protected $fillable = [
        'vendedor_id',
        'id_imovel',
        'qtd_tokens',
        'valor_unitario',
        'status',
    ];

    public function vendedor()
    {
        return $this->belongsTo(Investor::class, 'vendedor_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'id_imovel');
    }
}
