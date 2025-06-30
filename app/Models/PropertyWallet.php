<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'saldo_disponivel',
        'saldo_bloqueado',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
