<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Investor;
use App\Models\CarteiraInterna;

class TransacaoFinanceiraRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transacao_financeira_can_be_registered(): void
    {
        $this->withoutMiddleware();
        $investor = Investor::factory()->create();
        CarteiraInterna::factory()->create(['id_investidor' => $investor->id]);

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

        $this->assertDatabaseHas('carteiras_internas', [
            'id_investidor' => $investor->id,
            'saldo_disponivel' => 100,
        ]);
    }
}
