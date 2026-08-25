<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GreenCoinTransaction;
use App\Models\User;
use App\Services\GreenCoinService;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GreenCoinController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $user = auth()->user();

        return view('greencoin.index', [
            'user' => $user,
            'transactions' => GreenCoinTransaction::forUser($user->id)->latest()->paginate(15),
        ]);
    }

    public function transfer(Request $request, GreenCoinService $coins): RedirectResponse
    {
        $data = $request->validate([
            'phone_or_email' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $target = User::where('email', $data['phone_or_email'])
            ->orWhere('phone', $data['phone_or_email'])
            ->first();

        abort_unless($target, 422, 'Foydalanuvchi topilmadi.');

        $coins->transfer($request->user(), $target, $data['amount']);
        $this->logActivity('coin_transfer', "Transfer: {$target->name}ga {$data['amount']} coin");

        return back()->with('success', "{$target->name}ga {$data['amount']} GreenCoin yuborildi.");
    }

    public function leaderboard(): View
    {
        return view('greencoin.leaderboard', [
            'topUsers' => User::where('is_banned', false)
                ->orderByDesc('greencoin_balance')
                ->limit(20)
                ->get(['id', 'name', 'avatar', 'greencoin_balance', 'login_streak']),
            'me' => auth()->user(),
        ]);
    }
}
