<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Services\CsvExporter;
use App\Services\TotpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $tokens = ApiToken::where('user_id', $request->user()->id)->latest()->get();

        return view('settings.index', [
            'user' => $request->user(),
            'tokens' => $tokens,
        ]);
    }

    public function enableTwoFactor(Request $request, TotpService $totp): RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_secret) {
            $secret = $user->two_factor_secret;
        } else {
            $secret = $totp->generateSecret();
            $user->update([
                'two_factor_secret' => $secret,
                'two_factor_recovery_codes' => array_map(
                    fn (string $code) => Hash::make($code),
                    $totp->makeRecoveryCodes()
                ),
            ]);
        }

        return back()->with('otpauth_uri', $totp->otpauthUri($secret, $user->email));
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return back()->with('success', '2FA o‘chirildi.');
    }

    public function createApiToken(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ]);

        $plainToken = Str::random(48);

        ApiToken::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'token_hash' => ApiToken::hashToken($plainToken),
            'abilities' => ['*'],
            'expires_at' => now()->addYear(),
        ]);

        return response()->json([
            'token' => $plainToken,
            'message' => 'Token faqat bir marta ko‘rsatiladi.',
        ]);
    }

    public function revokeApiToken(Request $request, ApiToken $token): RedirectResponse
    {
        abort_unless($token->user_id === $request->user()->id, 403);

        $token->delete();

        return back()->with('success', 'Token bekor qilindi.');
    }

    public function exportData(Request $request, CsvExporter $csv): StreamedResponse
    {
        $user = $request->user();

        return $csv->download('ambarella-data.csv', [
            ['Bo‘lim', 'Ma’lumot'],
            ['Ism', $user->name],
            ['Email', $user->email],
            ['Telefon', $user->phone ?? '—'],
            ['Balans', $user->greencoin_balance],
            ...$user->transactions->map(fn ($tx) => [
                'Tranzaksiya', "{$tx->description} ({$tx->amount})",
            ])->all(),
        ]);
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'confirm' => ['required', 'string', 'in:DELETE'],
        ]);

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')->with('success', 'Hisobingiz o‘chirildi.');
    }
}
