<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\ForumTopic;
use App\Models\News;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    private const ALLOWED = [
        'news' => News::class,
        'blog_post' => BlogPost::class,
        'forum_topic' => ForumTopic::class,
    ];

    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $type = self::ALLOWED[$request->commentable_type] ?? null;

        abort_unless($type, 422, 'Noto‘g‘ri tur.');

        if ($request->parent_id) {
            abort_unless(Comment::whereKey($request->parent_id)->exists(), 422, 'Ota izoh topilmadi.');
        }

        Comment::create([
            'user_id' => $request->user()->id,
            'commentable_type' => $type,
            'commentable_id' => $request->commentable_id,
            'parent_id' => $request->parent_id,
            'body' => strip_tags($request->body),
        ]);

        return back()->with('success', 'Izoh qo‘shildi.');
    }
}
