<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Http\Requests\Eco\CompleteEcoTaskRequest;
use App\Models\EcoTask;
use App\Models\EcoTaskCompletion;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EcoTaskController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $today = today()->toDateString();

        $tasks = EcoTask::active()
            ->withCount(['completions as approved_count' => fn ($q) => $q->where('status', 'approved')])
            ->get();

        $myCompletions = EcoTaskCompletion::where('user_id', $user->id)
            ->whereDate('completed_on', $today)
            ->get()
            ->keyBy('eco_task_id');

        return view('eco.index', [
            'tasks' => $tasks,
            'myCompletions' => $myCompletions,
            'today' => $today,
        ]);
    }

    public function complete(CompleteEcoTaskRequest $request, EcoTask $task, ImageService $images, ActivityLogger $activity): RedirectResponse
    {
        abort_if(! $task->is_active, 404);

        $completion = EcoTaskCompletion::create([
            'eco_task_id' => $task->id,
            'user_id' => $request->user()->id,
            'proof_text' => $request->proof_text,
            'photo_path' => $images->upload($request->file('photo'), 'eco-proofs', 1600),
            'completed_on' => today(),
            'status' => $task->requires_proof ? 'pending' : 'approved',
        ]);

        // Auto-approve tasks without proof requirement (frontend-only completion).
        if ($completion->status === ModerationStatus::Approved) {
            app(\App\Services\GreenCoinService::class)->credit(
                $request->user(),
                $task->reward,
                "Eko vazifa: {$task->title}",
                reference: $completion
            );
        }

        $activity->log($request->user(), 'eco_complete', "Eko vazifa bajarildi: {$task->title}");

        return back()->with('success', $completion->status === ModerationStatus::Approved
            ? "Tabriklaymiz! +{$task->reward} GreenCoin qo‘shildi."
            : 'Vazifa yuborildi. Tasdiqlangach GreenCoin qo‘shiladi.');
    }
}
