<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\Investor;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvestmentFactory extends Factory
{
    protected $model = Investment::class;

    public function definition()
    {
        return [
            'id_investidor' => Investor::factory(),
            'id_imovel' => Property::factory(),
            'qtd_tokens' => $this->faker->numberBetween(1, 100),
            'valor_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'data_compra' => $this->faker->dateTime(),
            'origem' => $this->faker->randomElement(['plataforma', 'p2p']),
            'status' => 'ativo',
        ];
    }
}
