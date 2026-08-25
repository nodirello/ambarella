<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\GreenCoinTransaction;
use App\Models\Job;
use App\Models\PlatformEvent;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'user' => $user,
            'stats' => [
                'balance' => $user->greencoin_balance,
                'streak' => $user->login_streak,
                'applications' => $user->applications()->count(),
                'tasks_today' => DailyTask::where('user_id', $user->id)
                    ->whereDate('due_date', today())
                    ->where('is_done', false)
                    ->count(),
            ],
            'recent_transactions' => GreenCoinTransaction::forUser($user->id)->latest()->take(5)->get(),
            'recommended_jobs' => Job::active()->latest()->take(4)->get(),
            'upcoming_events' => PlatformEvent::upcoming()->take(3)->get(),
            'today' => Carbon::today()->translatedFormat('l, d F'),
        ]);
    }
}
