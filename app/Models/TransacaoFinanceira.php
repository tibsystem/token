<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransacaoFinanceira extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * This ensures the model uses the correct plural form
     * defined in the migration.
     */
    protected $table = 'transacoes_financeiras';

    public $incrementing = false;
    protected $keyType = 'string';

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
