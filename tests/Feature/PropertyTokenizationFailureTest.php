<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\Property;
use App\Models\SmartContractModel;

class PropertyTokenizationFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_deployment_returns_error(): void
    {
        $this->withoutMiddleware();

        $property = Property::factory()->create();
        $model = SmartContractModel::create([
            'name' => 'Test',
            'type' => 'erc20',
            'description' => 'desc',
            'solidity_code' => 'pragma solidity ^0.8.0;',
            'version' => '1.0',
        ]);

        Artisan::shouldReceive('call')->once()->andReturn(1);

        $response = $this->postJson('/api/properties/' . $property->id . '/tokenize', [
            'contract_model_id' => $model->id,
            'token_name' => 'Token',
            'token_symbol' => 'TKN',
            'total_supply' => 1000,
        ]);

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Tokenization failed']);
    }
}

