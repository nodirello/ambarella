<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->referral_code ??= $user->generateReferralCode();
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('profile_completed') && $user->profile_completed) {
            app(\App\Services\ReferralService::class)->rewardIfEligible($user);
        }
    }
}
