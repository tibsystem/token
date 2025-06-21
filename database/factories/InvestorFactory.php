<?php

namespace Database\Factories;

use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvestorFactory extends Factory
{
    protected $model = Investor::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'documento' => (string) $this->faker->randomNumber(9, true),
            'telefone' => $this->faker->phoneNumber(),
            'senha_hash' => bcrypt('password'),
            'status_kyc' => 'pendente',
            'carteira_blockchain' => $this->faker->sha256(),
        ];
    }
}
