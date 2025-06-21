<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_investor_can_be_registered(): void
    {
        $response = $this->postJson('/api/investors', [
            'nome' => 'John Doe',
            'email' => 'john@example.com',
            'documento' => '12345678901',
            'telefone' => '123456789',
            'senha' => 'secret123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('investors', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('carteiras_internas', [
            'id_investidor' => 1
        ]);
    }
}
