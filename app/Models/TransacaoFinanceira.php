<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransacaoFinanceira extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'transacoes_financeiras';

    protected $fillable = [
        'id',
        'id_investidor',
        'tipo',
        'valor',
        'status',
        'referencia',
        'data_transacao',
    ];
}
