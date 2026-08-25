<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DailyTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyTaskController extends Controller
{
    public function index(Request $request): View
    {
        return view('daily-tasks.index', [
            'tasks' => DailyTask::where('user_id', $request->user()->id)
                ->whereDate('due_date', today())
                ->orderBy('priority')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        DailyTask::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => today(),
            'priority' => $data['priority'] ?? 'medium',
        ]);

        return back()->with('success', 'Vazifa qo‘shildi.');
    }

    public function toggle(Request $request, DailyTask $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->update([
            'is_done' => ! $task->is_done,
            'completed_at' => $task->is_done ? null : now(),
        ]);

        return back();
    }

    public function destroy(Request $request, DailyTask $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->delete();

        return back()->with('success', 'Vazifa o‘chirildi.');
    }
}
