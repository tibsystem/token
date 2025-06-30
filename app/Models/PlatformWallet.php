<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'saldo_disponivel',
        'saldo_bloqueado',
    ];
}
