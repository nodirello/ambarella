<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    private const CODE_TTL = 300; // 5 minutes

    public function __construct(
        private readonly TelegramService $telegram,
        private readonly ActivityLogger $activity,
    ) {
    }

    /**
     * Telegram Login Widget callback: validates the hash and signs the user in.
     */
    public function callback(Request $request): RedirectResponse
    {
        abort_unless($this->telegram->enabled(), 404);

        $params = $request->query();

        if (! $this->validateWidgetHash($params)) {
            return redirect()->route('login')->withErrors(['telegram' => 'Telegram ma’lumotlari tasdiqlanmadi.']);
        }

        $chatId = (string) $params['id'];
        $telegramUser = TelegramUser::updateOrCreate(
            ['chat_id' => $chatId],
            [
                'first_name' => $params['first_name'] ?? null,
                'last_name' => $params['last_name'] ?? null,
                'username' => $params['username'] ?? null,
                'last_interaction_at' => now(),
            ]
        );

        $user = $telegramUser->user;

        if (! $user) {
            // Attach to an existing account by email (optional query param) or create a new one.
            $user = User::create([
                'name' => trim(($params['first_name'] ?? '').' '.($params['last_name'] ?? '')),
                'email' => strtolower(trim($params['username'] ?? '')).'@telegram.ambarella.uz',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
            $telegramUser->update(['user_id' => $user->id]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $this->activity->logAuth($user, 'Telegram orqali kirdi');

        return redirect()->route('dashboard.index');
    }

    /**
     * Step 1: request a one-time code to the user's phone via Telegram bot.
     */
    public function sendCode(Request $request): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:20']]);

        $normalized = preg_replace('/[^0-9]/', '', $request->phone);
        $telegramUser = TelegramUser::where('phone', $normalized)->first();

        if (! $telegramUser) {
            return response()->json(['message' => 'Bu raqam botga ulanmagan. Avval @AmbarellaBot ga /start yuboring.'], 422);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put('tg_auth:'.$normalized, hash('sha256', $code), self::CODE_TTL);

        $this->telegram->sendMessage(
            $telegramUser->chat_id,
            "🔐 AMBARELLA kirish kodi: <b>{$code}</b>\nKod 5 daqiqa amal qiladi."
        );

        return response()->json(['message' => 'Kod Telegramga yuborildi.']);
    }

    /**
     * Step 2: verify code -> sign in.
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $normalized = preg_replace('/[^0-9]/', '', $request->phone);
        $saved = Cache::get('tg_auth:'.$normalized);

        if (! $saved || ! hash_equals($saved, hash('sha256', $request->code))) {
            return response()->json(['message' => 'Kod noto‘g‘ri yoki muddati o‘tgan.'], 422);
        }

        $telegramUser = TelegramUser::where('phone', $normalized)->first();

        if (! $telegramUser?->user) {
            return response()->json(['message' => 'Hisob topilmadi. Avval saytda ro‘yxatdan o‘ting.'], 422);
        }

        Cache::forget('tg_auth:'.$normalized);

        Auth::login($telegramUser->user);
        $request->session()->regenerate();
        $this->activity->logAuth($telegramUser->user, 'Telegram kodi orqali kirdi');

        return response()->json(['redirect' => route('dashboard.index')]);
    }

    private function validateWidgetHash(array $params): bool
    {
        $hash = $params['hash'] ?? null;
        unset($params['hash']);

        if (! $hash) {
            return false;
        }

        ksort($params);
        $checkString = collect($params)
            ->map(fn ($v, $k) => "{$k}={$v}")
            ->implode("\n");

        $secretKey = hash('sha256', $this->telegram->token());

        return hash_equals(hash_hmac('sha256', $checkString, $secretKey), $hash);
    }
}
