<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\GreenCoinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGreenCoinController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.greencoin.index', [
            'users' => User::with('businessProfile')
                ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                ->orderByDesc('greencoin_balance')
                ->limit(50)
                ->get(),
        ]);
    }

    public function adjust(Request $request, User $user, GreenCoinService $coins, ActivityLogger $activity): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'between:-100000,100000'],
            'reason' => ['required', 'string', 'max:300'],
        ]);

        $coins->adjust($user, $data['amount'], $data['reason']);
        $activity->log($request->user(), 'coin_adjust', "Balans tuzatildi: {$user->email} ({$data['amount']})");

        return back()->with('success', 'Balans tuzatildi.');
    }
}
