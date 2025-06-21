<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_registered(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/properties', [
            'titulo' => 'House',
            'descricao' => 'A big house',
            'localizacao' => 'City',
            'valor_total' => 1000,
            'qtd_tokens' => 1,
            'status' => 'ativo',
            'data_tokenizacao' => now(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('properties', [
            'titulo' => 'House',
            'user_id' => $user->id,
        ]);
    }
}
