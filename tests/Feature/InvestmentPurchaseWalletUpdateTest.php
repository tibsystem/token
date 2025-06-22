<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Investor;
use App\Models\Property;
use App\Models\CarteiraInterna;

class InvestmentPurchaseWalletUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_updates_wallet_balance(): void
    {
        $this->withoutMiddleware();
        $investor = Investor::factory()->create();
        CarteiraInterna::factory()->create([
            'id_investidor' => $investor->id,
            'saldo_disponivel' => 500,
        ]);
        $property = Property::factory()->create();

        $response = $this->postJson('/api/investments/purchase', [
            'id_investidor' => $investor->id,
            'id_imovel' => $property->id,
            'qtd_tokens' => 2,
            'valor_unitario' => 10,
            'data_compra' => now()->toDateTimeString(),
            'origem' => 'plataforma',
            'status' => 'ativo',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transacoes_financeiras', [
            'id_investidor' => $investor->id,
            'tipo' => 'compra_token',
        ]);
        $this->assertDatabaseHas('carteiras_internas', [
            'id_investidor' => $investor->id,
            'saldo_disponivel' => 480,
        ]);
    }
}

