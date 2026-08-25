<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(): View
    {
        return view('polls.index', [
            'polls' => Poll::open()->with('options.votes')->latest()->paginate(6),
        ]);
    }

    public function show(Poll $poll): View
    {
        return view('polls.show', [
            'poll' => $poll->load('options.votes'),
            'myVote' => auth()->check() ? $poll->userVote(auth()->id()) : null,
        ]);
    }

    public function vote(Request $request, Poll $poll, ActivityLogger $activity): RedirectResponse
    {
        abort_if(! $poll->is_active || $poll->ends_at?->isPast(), 422, 'So‘rovnoma yopilgan.');

        $data = $request->validate([
            'option_id' => ['required', 'integer', 'exists:poll_options,id,poll_id,'.$poll->id],
        ]);

        $already = $poll->votes()->where('user_id', $request->user()->id)->exists();

        if ($already && ! $poll->allow_multiple) {
            return back()->withErrors(['option_id' => 'Siz allaqachon ovoz bergansiz.']);
        }

        \App\Models\PollVote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $data['option_id'],
            'user_id' => $request->user()->id,
        ]);

        $activity->log($request->user(), 'poll_vote', "So‘rovnomada ovoz: {$poll->title}");

        return back()->with('success', 'Ovozingiz hisobga olindi.');
    }
}
