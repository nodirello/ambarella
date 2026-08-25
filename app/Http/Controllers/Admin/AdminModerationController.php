<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\EcoTaskCompletion;
use App\Services\ActivityLogger;
use App\Services\GreenCoinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Unified moderation queue for user-generated content.
 */
class AdminModerationController extends Controller
{
    public function index(): View
    {
        return view('admin.moderation.index', [
            'blogPosts' => BlogPost::pending()->with('author')->oldest()->take(30)->get(),
            'ecoCompletions' => EcoTaskCompletion::pending()->with(['user', 'task'])->oldest()->take(30)->get(),
            'comments' => Comment::where('is_approved', false)->with('user')->latest()->take(30)->get(),
        ]);
    }

    public function approveBlogPost(BlogPost $post, ActivityLogger $activity): RedirectResponse
    {
        $post->update(['status' => ModerationStatus::Approved, 'published_at' => now()]);
        $activity->log($request = request()->user(), 'moderation', "Maqola tasdiqlandi: {$post->title}");

        return back()->with('success', 'Maqola tasdiqlandi.');
    }

    public function rejectBlogPost(BlogPost $post, ActivityLogger $activity): RedirectResponse
    {
        $post->update(['status' => ModerationStatus::Rejected]);
        $activity->log(request()->user(), 'moderation', "Maqola rad etildi: {$post->title}");

        return back()->with('success', 'Maqola rad etildi.');
    }

    public function approveEco(EcoTaskCompletion $completion, ActivityLogger $activity, GreenCoinService $coins): RedirectResponse
    {
        abort_if($completion->status !== ModerationStatus::Pending, 422, 'Bu ariza ko‘rib chiqilgan.');

        $completion->update([
            'status' => ModerationStatus::Approved,
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        $coins->credit(
            $completion->user,
            $completion->task->reward,
            "Eko vazifa tasdiqlandi: {$completion->task->title}",
            reference: $completion
        );

        $activity->log(request()->user(), 'moderation', "Eko vazifa tasdiqlandi: {$completion->task->title}");

        return back()->with('success', 'Tasdiqlandi, GreenCoin berildi.');
    }

    public function rejectEco(EcoTaskCompletion $completion, ActivityLogger $activity): RedirectResponse
    {
        abort_if($completion->status !== ModerationStatus::Pending, 422, 'Bu ariza ko‘rib chiqilgan.');

        $completion->update([
            'status' => ModerationStatus::Rejected,
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        $activity->log(request()->user(), 'moderation', "Eko vazifa rad etildi: {$completion->task->title}");

        return back()->with('success', 'Ariza rad etildi.');
    }

    public function toggleComment(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => ! $comment->is_approved]);

        return back()->with('success', $comment->is_approved ? 'Izoh ko‘rsatildi.' : 'Izoh yashirildi.');
    }
}
