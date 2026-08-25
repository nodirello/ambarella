<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activity,
        private readonly TelegramService $telegram,
    ) {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            $user->forceFill([
                'last_login_at' => now(),
                'login_streak' => $this->nextStreak((int) $user->login_streak, $user->last_login_at),
            ])->save();

            $this->activity->logAuth($user, 'Tizimga kirdi');

            return redirect()->intended(route('dashboard.index'));
        }

        \App\Http\Middleware\BlockSuspiciousIps::recordFailedLogin($request->ip());
        $this->activity->logAuth(null, "Muvaffaqiyatsiz kirish urinishi: {$request->email}");

        return back()->withErrors(['email' => 'Email yoki parol noto‘g‘ri.'])->onlyInput('email');
    }

    public function showRegister(Request $request): View
    {
        if ($request->string('ref')->isNotEmpty()) {
            $request->session()->put('referral_code', $request->string('ref')->toString());
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->safe();

        $user = User::create([
            'name' => $data->name,
            'email' => strtolower(trim($data->email)),
            'phone' => $data->phone ? preg_replace('/[^+0-9]/', '', $data->phone) : null,
            'password' => Hash::make($data->password),
        ]);

        $refCode = $data->ref ?? $request->session()->pull('referral_code');
        if ($refCode) {
            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer && $referrer->isNot($user)) {
                $user->update(['referred_by' => $referrer->id]);
            }
        }

        Auth::login($user);
        $this->activity->logAuth($user, 'Yangi foydalanuvchi ro‘yxatdan o‘tdi');

        return redirect()->route('onboarding.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function nextStreak(int $current, ?\DateTimeInterface $lastLogin): int
    {
        if (! $lastLogin) {
            return 1;
        }

        $last = \Illuminate\Support\Carbon::parse($lastLogin);

        if ($last->isToday()) {
            return max($current, 1);
        }

        return $last->isYesterday() ? $current + 1 : 1;
    }
}
