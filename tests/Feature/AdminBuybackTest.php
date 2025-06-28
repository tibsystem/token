<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\CarteiraInterna;

class AdminBuybackTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_buyback_updates_wallet_and_records_transaction(): void
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create(['tipo' => 'admin']);
        $property = Property::factory()->create(['user_id' => $admin->id]);
        $investor = Investor::factory()->create();
        CarteiraInterna::factory()->create(['id_investidor' => $investor->id, 'saldo_disponivel' => 0]);
        Investment::factory()->create([
            'id_investidor' => $investor->id,
            'id_imovel' => $property->id,
            'qtd_tokens' => 10,
            'valor_unitario' => 2,
        ]);

        $response = $this->actingAs($admin, 'api')->postJson('/api/admin/imoveis/' . $property->id . '/buyback', [
            'valor_pago' => 2.5,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carteiras_internas', [
            'id_investidor' => $investor->id,
            'saldo_disponivel' => 25,
        ]);

        $this->assertDatabaseHas('transacoes_financeiras', [
            'id_investidor' => $investor->id,
            'tipo' => 'rendimento',
            'valor' => 25,
        ]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'vendido',
        ]);
    }
}
