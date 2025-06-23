<?php

namespace App\Helpers;

use App\Models\TransactionLog;
use Illuminate\Support\Str;

class LogTransacaoHelper
{
    public static function registrar(string $tipo, array $dados, $user = null, $imovel_id = null): void
    {
        TransactionLog::create([
            'id' => (string) Str::uuid(),
            'tipo' => $tipo,
            'id_usuario' => $user ? $user->id : null,
            'id_imovel' => $imovel_id,
            'dados' => $dados,
            'ip_origem' => request()->ip(),
            'navegador' => request()->header('User-Agent'),
            'criado_em' => now(),
        ]);
    }
}
