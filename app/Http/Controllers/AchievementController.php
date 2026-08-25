<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $earned = $user->achievements()->pluck('achievements.id');

        return view('achievements.index', [
            'achievements' => Achievement::where('is_active', true)->get(),
            'earnedIds' => $earned,
        ]);
    }
}
