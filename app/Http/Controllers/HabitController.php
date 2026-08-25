<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitController extends Controller
{
    public function index(Request $request): View
    {
        return view('habits.index', [
            'habits' => Habit::where('user_id', $request->user()->id)
                ->with('logs')
                ->orderByDesc('is_active')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'frequency' => ['nullable', 'in:daily,weekly'],
        ]);

        Habit::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'frequency' => $data['frequency'] ?? 'daily',
        ]);

        return back()->with('success', 'Odat yaratildi. Har kuni davom eting!');
    }

    public function complete(Request $request, Habit $habit): RedirectResponse
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $exists = HabitLog::where('habit_id', $habit->id)
            ->whereDate('completed_on', today())
            ->exists();

        if ($exists) {
            return back()->withErrors(['habit' => 'Bugun allaqachon bajarilgan.']);
        }

        HabitLog::create([
            'habit_id' => $habit->id,
            'user_id' => $request->user()->id,
            'completed_on' => today(),
        ]);

        $streak = $this->streakFor($habit);
        $habit->update([
            'current_streak' => $streak,
            'best_streak' => max($streak, (int) $habit->best_streak),
        ]);

        return back()->with('success', "Ajoyib! {$streak} kunlik seriya. 🔥");
    }

    public function destroy(Request $request, Habit $habit): RedirectResponse
    {
        abort_unless($habit->user_id === $request->user()->id, 403);

        $habit->delete();

        return back()->with('success', 'Odat o‘chirildi.');
    }

    private function streakFor(Habit $habit): int
    {
        $streak = 0;
        $day = today();

        while ($habit->logs()->whereDate('completed_on', $day)->exists()) {
            $streak++;
            $day->subDay();
        }

        return $streak;
    }
}
