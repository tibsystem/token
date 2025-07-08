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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'document' => (string) $this->faker->randomNumber(9, true),
            'phone' => $this->faker->phoneNumber(),
            'password' => bcrypt('password'),
            'status_kyc' => 'pendente',
            'wallet_blockchain' => $this->faker->sha256(),
            'wallet_private_key' => bin2hex(random_bytes(32)),
        ];
    }
}
