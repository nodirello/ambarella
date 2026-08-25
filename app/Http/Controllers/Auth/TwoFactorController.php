<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TwoFactorChallengeRequest;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function showChallenge(Request $request): View|RedirectResponse
    {
        if (! $request->user()?->two_factor_secret) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.two-factor');
    }

    public function challenge(TwoFactorChallengeRequest $request, TotpService $totp): RedirectResponse
    {
        $user = $request->user();

        $valid = $totp->verify($user->two_factor_secret, $request->code);

        // Fallback: one-time recovery codes (stored hashed)
        if (! $valid) {
            $codes = collect($user->two_factor_recovery_codes ?? []);
            $index = $codes->search(
                fn (string $hash) => Hash::check(strtoupper($request->code), $hash)
            );

            if ($index !== false) {
                $codes->forget($index);
                $user->update(['two_factor_recovery_codes' => $codes->values()->all()]);
                $valid = true;
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'Kod noto‘g‘ri.']);
        }

        $request->session()->put('2fa.verified', true);

        return redirect()->intended(route('dashboard.index'));
    }

    public function recoveryCodes(Request $request, TotpService $totp): View|RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user->two_factor_secret, 404);

        return view('auth.recovery-codes', [
            'codes' => collect($user->two_factor_recovery_codes ?? [])
                ->map(fn (string $hash) => $hash)
                ->map(fn () => '••••-••••')
                ->all(),
        ]);
    }
}
