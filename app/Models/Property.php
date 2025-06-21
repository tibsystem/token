<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;


    protected $fillable = [
        'titulo',
        'descricao',
        'localizacao',
        'valor_total',
        'qtd_tokens',
        'modelo_smart_id',
        'status',
        'data_tokenizacao',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class, 'id_imovel');
    }
}
