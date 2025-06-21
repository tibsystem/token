<?php

namespace Database\Factories;

use App\Models\TransacaoFinanceira;
use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransacaoFinanceiraFactory extends Factory
{
    protected $model = TransacaoFinanceira::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'id_investidor' => Investor::factory(),
            'tipo' => $this->faker->randomElement(['deposito','saque','rendimento','taxa']),
            'valor' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pendente','concluido','falhou']),
            'referencia' => $this->faker->sentence(),
            'data_transacao' => $this->faker->dateTime(),
        ];
    }
}
