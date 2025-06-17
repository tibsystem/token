<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_registered(): void
    {
        $response = $this->postJson('/api/properties', [
            'title' => 'House',
            'description' => 'A big house',
            'location' => 'City',
            'price' => 1000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('properties', ['title' => 'House']);
    }
}
