<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Forum\StoreReplyRequest;
use App\Http\Requests\Forum\StoreTopicRequest;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Traits\TracksViews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    use TracksViews;

    public function index(Request $request): View
    {
        return view('forum.index', [
            'topics' => ForumTopic::withCount('replies')
                ->with('author')
                ->when($request->category, fn ($q, $c) => $q->where('category', $c))
                ->ordered()
                ->paginate(15),
            'categories' => ['umumiy', 'dasturlash', 'bandlik', 'ta\'lim', 'ekologiya', 'biznes', 'psixologiya'],
        ]);
    }

    public function create(): View
    {
        return view('forum.create');
    }

    public function store(StoreTopicRequest $request): RedirectResponse
    {
        $topic = ForumTopic::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'category' => $request->category,
            'body' => $request->body,
        ]);

        return redirect()->route('forum.show', $topic)->with('success', 'Mavzu yaratildi.');
    }

    public function show(ForumTopic $topic): View
    {
        if ($topic->is_locked) {
            abort(403, 'Mavzu yopilgan.');
        }

        $this->registerView($topic, $topic->title, route('forum.show', $topic));

        return view('forum.show', [
            'topic' => $topic->load(['author', 'replies.author']),
        ]);
    }

    public function reply(StoreReplyRequest $request, ForumTopic $topic): RedirectResponse
    {
        abort_if($topic->is_locked, 403, 'Mavzu yopilgan.');

        ForumReply::create([
            'forum_topic_id' => $topic->id,
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return back()->with('success', 'Javob qo‘shildi.');
    }

    public function markBest(Request $request, ForumReply $reply): RedirectResponse
    {
        abort_if($reply->topic->user_id !== $request->user()->id && ! $request->user()->isStaff(), 403);

        ForumReply::where('forum_topic_id', $reply->forum_topic_id)->update(['is_best' => false]);
        $reply->update(['is_best' => true]);
        $reply->topic->update(['is_solved' => true]);

        return back()->with('success', 'Eng yaxshi javob belgilandi.');
    }
}
