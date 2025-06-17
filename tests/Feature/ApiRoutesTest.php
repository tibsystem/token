<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Property;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_login_returns_unauthorized()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'fake@example.com',
            'password' => 'invalid'
        ]);

        $response->assertStatus(401);
    }

    public function test_auth_register_creates_user()
    {
        $response = $this->postJson('/api/auth/register', [
            'nome' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'secret',
            'tipo' => 'investidor'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'tipo' => 'investidor'
        ]);
    }

    public function test_user_profile_route()
    {
        $this->withoutMiddleware();
        $this->getJson('/api/user/profile')->assertStatus(200);
    }

    public function test_wallet_routes()
    {
        $this->withoutMiddleware();
        $this->getJson('/api/wallet')->assertStatus(200);
        $this->postJson('/api/wallet/add-funds')->assertStatus(200);
        $this->postJson('/api/wallet/withdraw')->assertStatus(200);
    }

    public function test_property_resource_routes()
    {
        $this->withoutMiddleware();
        $property = Property::create([
            'title' => 'Test',
            'location' => 'Loc',
            'price' => 1,
        ]);

        $this->getJson('/api/properties')->assertStatus(200);
        $this->getJson('/api/properties/' . $property->id)->assertStatus(200);
        $this->putJson('/api/properties/' . $property->id, ['title' => 'Changed'])
            ->assertStatus(200);
        $this->deleteJson('/api/properties/' . $property->id)->assertStatus(200);
    }

    public function test_property_tokens_route()
    {
        $this->withoutMiddleware();
        $property = Property::create([
            'title' => 'Tokens',
            'location' => 'Loc',
            'price' => 2,
        ]);
        $this->getJson('/api/properties/' . $property->id . '/tokens')
            ->assertStatus(200);
    }

    public function test_investment_routes()
    {
        $this->withoutMiddleware();
        $this->postJson('/api/investments/purchase')->assertStatus(200);
        $this->getJson('/api/investments/history')->assertStatus(200);
    }

    public function test_support_ticket_routes()
    {
        $this->withoutMiddleware();
        $this->getJson('/api/support-tickets')->assertStatus(200);
        $this->postJson('/api/support-tickets', [])->assertStatus(201);
        $this->getJson('/api/support-tickets/1')->assertStatus(200);
        $this->putJson('/api/support-tickets/1', [])->assertStatus(200);
        $this->deleteJson('/api/support-tickets/1')->assertStatus(200);
    }
}
