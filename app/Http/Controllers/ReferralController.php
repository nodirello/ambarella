<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class ReferralController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('referral.index', [
            'user' => $user,
            'referrals' => $user->referrals()->latest()->take(25)->get(),
        ]);
    }
}
