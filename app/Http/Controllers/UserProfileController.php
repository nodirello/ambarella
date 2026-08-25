<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_if($user->is_banned || ! $user->profile_completed, 404);

        return view('user-profile.show', [
            'profile' => $user,
            'stats' => [
                'achievements' => $user->achievements()->count(),
                'referrals' => $user->referral_count,
                'streak' => $user->login_streak,
            ],
        ]);
    }
}
