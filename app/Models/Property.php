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
        'qtd_tokens_original',
        'modelo_smart_id',
        'contract_model_id',
        'contract_address',
        'token_symbol',
        'token_name',
        'contract_abi',
        'total_supply',
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

    /**
     * Diminui a quantidade disponível de tokens do imóvel.
     *
     * @param int $qtd Quantidade de tokens a ser subtraída
     * @return bool      True em caso de sucesso
     */
    public function decreaseTokens(int $qtd): bool
    {
        if ($qtd < 0 || $this->qtd_tokens < $qtd) {
            return false;
        }

        $this->qtd_tokens -= $qtd;
        return $this->save();
    }
}
