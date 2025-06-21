<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Investor;

class TransacaoFinanceiraRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transacao_financeira_can_be_registered(): void
    {
        $investor = Investor::factory()->create();

        $response = $this->postJson('/api/transacoes-financeiras', [
            'id_investidor' => $investor->id,
            'tipo' => 'deposito',
            'valor' => 100,
            'status' => 'pendente',
            'data_transacao' => now()->toDateTimeString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transacoes_financeiras', [
            'id_investidor' => $investor->id,
            'tipo' => 'deposito',
        ]);
    }
}
