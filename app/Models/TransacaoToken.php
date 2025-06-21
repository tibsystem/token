<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransacaoToken extends Model
{
    use HasFactory;

    protected $table = 'transacoes_tokens';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'vendedor_id',
        'comprador_id',
        'id_imovel',
        'qtd_tokens',
        'valor_unitario',
        'data_transacao',
        'tx_hash',
        'status',
    ];
}
