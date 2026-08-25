<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const CODE_TTL = 600; // 10 minutes

    public function showForm(): View
    {
        return view('auth.forgot-password');
    }

    public function requestReset(Request $request, TelegramService $telegram): View|RedirectResponse
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $user = User::where('email', $request->email)->firstOrFail();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('password_reset:'.$user->email, $code, self::CODE_TTL);

        $telegramUser = $user->telegramUsers()->whereNotNull('chat_id')->first();

        if ($telegram && $telegramUser) {
            $telegram->sendMessage(
                $telegramUser->chat_id,
                "🔐 <b>AMBARELLA</b> parolni tiklash kodi: <b>{$code}</b>\nKod 10 daqiqa amal qiladi."
            );

            return view('auth.reset-code', ['email' => $user->email]);
        }

        try {
            Mail::raw(
                "AMBARELLA parolni tiklash kodi: {$code}\n\nKod 10 daqiqa davomida amal qiladi. Agar siz so‘ramagan bo‘lsangiz, xatni e’tiborsiz qoldiring.",
                fn ($message) => $message->to($user->email)->subject('AMBARELLA — Parolni tiklash kodi')
            );

            return view('auth.reset-code', ['email' => $user->email]);
        } catch (\Throwable) {
            return back()->withErrors(['email' => 'Kod yuborib bo‘lmadi. Qayta urinib ko‘ring.']);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $saved = Cache::get('password_reset:'.$request->email);

        if (! $saved || ! hash_equals($saved, $request->code)) {
            return back()->withErrors(['code' => 'Kod noto‘g‘ri yoki muddati o‘tgan.']);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->update(['password' => Hash::make($request->password)]);

        Cache::forget('password_reset:'.$request->email);

        return redirect()->route('login')->with('success', 'Parol yangilandi. Endi tizimga kiring.');
    }
}
