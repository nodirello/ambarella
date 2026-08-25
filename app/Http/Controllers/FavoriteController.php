<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    use LogsActivity;

    private const ALLOWED = [
        'job' => \App\Models\Job::class,
        'course' => \App\Models\Course::class,
        'news' => \App\Models\News::class,
        'startup' => \App\Models\Startup::class,
        'event' => \App\Models\PlatformEvent::class,
    ];

    public function index(Request $request): View
    {
        $favorites = Favorite::with('favoritable')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('favorites.index', ['favorites' => $favorites]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(self::ALLOWED))],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $modelClass = self::ALLOWED[$data['type']];
        $model = $modelClass::findOrFail($data['id']);

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $model->getKey())
            ->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'favoritable_type' => $modelClass,
                'favoritable_id' => $model->getKey(),
            ]);
            $added = true;
        }

        return response()->json(['added' => $added]);
    }
}
