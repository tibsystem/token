<?php

namespace Database\Factories;

use App\Models\CarteiraInterna;
use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarteiraInternaFactory extends Factory
{
    protected $model = CarteiraInterna::class;

    public function definition()
    {
        return [
            'id_investidor' => Investor::factory(),
            'endereco_wallet' => $this->faker->sha256(),
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ];
    }
}
