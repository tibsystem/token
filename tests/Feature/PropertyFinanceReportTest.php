<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Investor;
use App\Models\Investment;

class PropertyFinanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_property_financial_report(): void
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create(['tipo' => 'admin']);
        $property = Property::factory()->create([
            'user_id' => $admin->id,
            'qtd_tokens_original' => 100,
            'qtd_tokens' => 80,
        ]);

        $investor = Investor::factory()->create();
        Investment::factory()->create([
            'id_investidor' => $investor->id,
            'id_imovel' => $property->id,
            'qtd_tokens' => 20,
            'valor_unitario' => 5,
        ]);

        $response = $this->getJson('/api/admin/imoveis/' . $property->id . '/financeiro');

        $response->assertStatus(200)
            ->assertJsonFragment(['tokens_vendidos' => 20]);
    }
}

