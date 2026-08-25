<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_referral_rewards_both_sides_only_once(): void
    {
        $referrer = User::factory()->create();
        $user = User::factory()->create(['referred_by' => $referrer->id]);

        app(ReferralService::class)->rewardIfEligible($user);

        $this->assertSame(ReferralService::REFERRAL_BONUS, $user->fresh()->greencoin_balance);
        $this->assertSame(ReferralService::REFERRAL_BONUS, $referrer->fresh()->greencoin_balance);
        $this->assertSame(1, $referrer->fresh()->referral_count);
        $this->assertTrue($user->fresh()->referral_rewarded);

        // Second call must not double-pay.
        app(ReferralService::class)->rewardIfEligible($user->fresh());
        $this->assertSame(ReferralService::REFERRAL_BONUS, $user->fresh()->greencoin_balance);
    }

    public function test_users_without_referrer_get_nothing(): void
    {
        $user = User::factory()->create(['referred_by' => null]);

        app(ReferralService::class)->rewardIfEligible($user);

        $this->assertSame(0, $user->fresh()->greencoin_balance);
    }
}
