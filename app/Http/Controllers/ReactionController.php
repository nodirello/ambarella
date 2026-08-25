<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    private const ALLOWED = [
        'news' => \App\Models\News::class,
        'blog_post' => \App\Models\BlogPost::class,
        'forum_topic' => \App\Models\ForumTopic::class,
        'comment' => \App\Models\Comment::class,
    ];

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(self::ALLOWED))],
            'id' => ['required', 'integer', 'min:1'],
            'reaction' => ['required', 'in:like,love,wow,sad,savvy'],
        ]);

        $modelClass = self::ALLOWED[$data['type']];
        $model = $modelClass::findOrFail($data['id']);

        $existing = Reaction::where('user_id', $request->user()->id)
            ->where('reactable_type', $modelClass)
            ->where('reactable_id', $model->getKey())
            ->where('type', $data['reaction'])
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Reaction::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'reactable_type' => $modelClass,
                    'reactable_id' => $model->getKey(),
                ],
                ['type' => $data['reaction']]
            );
        }

        return response()->json([
            'count' => Reaction::where('reactable_type', $modelClass)->where('reactable_id', $model->getKey())->count(),
        ]);
    }
}
