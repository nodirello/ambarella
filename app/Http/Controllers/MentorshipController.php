<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\MentorTask;
use App\Models\Mentorship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorshipController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('mentorship.index', [
            'asMentor' => $user->mentorshipsAsMentor()->with(['student', 'tasks'])->latest()->get(),
            'asStudent' => \App\Models\Mentorship::where('student_id', $user->id)->with(['mentor', 'tasks'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('mentorship.create', [
            'mentors' => Mentor::available()->get(['id', 'name', 'role', 'skills', 'rating']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mentor_id' => ['required', 'exists:mentors,id'],
            'goal' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        abort_if(
            Mentorship::where('mentor_id', $data['mentor_id'])
                ->where('student_id', $request->user()->id)
                ->whereIn('status', ['pending', 'active'])
                ->exists(),
            422,
            'Bu mentor bilan faol mentorship mavjud.'
        );

        Mentorship::create([
            'mentor_id' => $data['mentor_id'],
            'student_id' => $request->user()->id,
            'goal' => $data['goal'],
        ]);

        return redirect()->route('mentorship.index')->with('success', 'Mentorship so‘rovi yuborildi.');
    }

    public function accept(Request $request, Mentorship $mentorship): RedirectResponse
    {
        abort_unless($mentorship->mentor?->user_id === $request->user()->id, 403);

        $mentorship->update(['status' => 'active', 'started_at' => now()]);

        return back()->with('success', 'Mentorship boshlandi.');
    }

    public function reject(Request $request, Mentorship $mentorship): RedirectResponse
    {
        abort_unless($mentorship->mentor?->user_id === $request->user()->id, 403);

        $mentorship->update(['status' => 'cancelled']);

        return back()->with('success', 'So‘rov rad etildi.');
    }

    public function addTask(Request $request, Mentorship $mentorship): RedirectResponse
    {
        $this->authorizeMentor($request, $mentorship);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);

        MentorTask::create([
            'mentorship_id' => $mentorship->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
        ]);

        return back()->with('success', 'Vazifa qo‘shildi.');
    }

    public function completeTask(Request $request, MentorTask $task): RedirectResponse
    {
        $mentorship = $task->mentorship->load('mentor');
        abort_unless(
            $mentorship->student_id === $request->user()->id
            || $mentorship->mentor?->user_id === $request->user()->id,
            403
        );

        $task->update(['status' => 'done', 'completed_at' => now()]);

        if ($mentorship->tasks()->where('status', '!=', 'done')->count() === 0) {
            $mentorship->update(['status' => 'completed', 'ended_at' => now()]);
        }

        return back()->with('success', 'Vazifa yakunlandi.');
    }

    private function authorizeMentor(Request $request, Mentorship $mentorship): void
    {
        abort_unless($mentorship->mentor?->user_id === $request->user()->id, 403);
    }
}
