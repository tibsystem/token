<?php

namespace Database\Factories;

use App\Models\Kyc;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KycFactory extends Factory
{
    protected $model = Kyc::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'document_type' => 'passport',
            'document_number' => (string) $this->faker->randomNumber(8),
        ];
    }
}
