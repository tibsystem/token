<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $table = 'transaction_logs';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'tipo',
        'id_usuario',
        'id_imovel',
        'dados',
        'ip_origem',
        'navegador',
        'criado_em',
    ];

    protected $casts = [
        'dados' => 'array',
    ];
}
