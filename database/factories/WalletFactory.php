<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'polygon_address' => $this->faker->regexify('[A-Fa-f0-9]{40}'),
            'private_key_enc' => bin2hex(random_bytes(32)),
            'saldo' => $this->faker->randomFloat(8, 0, 1000),
        ];
    }
}
