<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'polygon_address',
        'private_key_enc',
        'saldo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
