<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\StartupVote;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StartupController extends Controller
{
    public function index(): View
    {
        return view('startup.index', [
            'startups' => Startup::visible()->withCount('votes')->orderByDesc('votes_count')->paginate(9),
        ]);
    }

    public function show(Startup $startup): View
    {
        abort_unless($startup->is_approved, 404);

        return view('startup.show', [
            'startup' => $startup->load('owner', 'votes'),
            'voted' => auth()->check()
                ? StartupVote::where('startup_id', $startup->id)->where('user_id', auth()->id())->exists()
                : false,
        ]);
    }

    public function create(): View
    {
        return view('startup.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'min:50', 'max:5000'],
            'category' => ['required', 'string', 'max:80'],
            'stage' => ['required', 'in:idea,mvp,growth,scale'],
            'team_size' => ['required', 'integer', 'min:1', 'max:200'],
            'looking_for' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $startup = Startup::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('startups.show', $startup)->with('success', 'Startup joylandi.');
    }

    public function vote(Request $request, Startup $startup, ActivityLogger $activity): RedirectResponse
    {
        $voted = StartupVote::where('startup_id', $startup->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($voted) {
            StartupVote::where('startup_id', $startup->id)->where('user_id', $request->user()->id)->delete();
            $startup->decrement('votes_count');

            return back()->with('success', 'Ovoz olib tashlandi.');
        }

        StartupVote::create([
            'startup_id' => $startup->id,
            'user_id' => $request->user()->id,
        ]);
        $startup->increment('votes_count');
        $activity->log($request->user(), 'startup_vote', "Ovoz: {$startup->name}");

        return back()->with('success', 'Ovoshingiz qabul qilindi.');
    }
}
