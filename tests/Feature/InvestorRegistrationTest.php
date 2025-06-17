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
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('investors', ['email' => 'john@example.com']);
    }
}
