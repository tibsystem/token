<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Kyc;
use App\Models\Investment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_wallet()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->wallet->is($wallet));
    }

    public function test_user_has_many_kycs()
    {
        $user = User::factory()->create();
        $kycs = Kyc::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->kyc);
        $this->assertTrue($user->kyc->first()->is($kycs->first()));
    }

    public function test_user_has_many_investments()
    {
        $user = User::factory()->create();
        $investments = Investment::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->investments);
        $this->assertTrue($user->investments->first()->is($investments->first()));
    }
}
