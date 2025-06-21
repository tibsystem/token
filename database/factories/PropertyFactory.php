<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->word(),
            'descricao' => $this->faker->sentence(),
            'localizacao' => $this->faker->city(),
            'valor_total' => $this->faker->randomFloat(2, 10000, 100000),
            'qtd_tokens' => $this->faker->numberBetween(1, 1000),
            'modelo_smart_id' => null,
            'status' => 'ativo',
            'data_tokenizacao' => now(),
            'user_id' => User::factory(),
        ];
    }
}

