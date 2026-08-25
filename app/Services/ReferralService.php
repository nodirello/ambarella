<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    public const REFERRAL_BONUS = 50;

    public function __construct(private readonly GreenCoinService $coins)
    {
    }

    /**
     * Both sides receive a bonus exactly once, when the newcomer's profile is completed.
     */
    public function rewardIfEligible(User $user): void
    {
        if (! $user->referred_by || $user->referral_rewarded) {
            return;
        }

        DB::transaction(function () use ($user): void {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($user->referral_rewarded || ! $user->referred_by) {
                return;
            }

            $referrer = User::find($user->referred_by);
            if (! $referrer) {
                return;
            }

            $this->coins->credit($user, self::REFERRAL_BONUS, "Taklif bonusi ({$referrer->name})");
            $this->coins->credit($referrer, self::REFERRAL_BONUS, "Referal bonus: {$user->name}");

            $referrer->increment('referral_count');
            $user->update(['referral_rewarded' => true]);
        });
    }
}
