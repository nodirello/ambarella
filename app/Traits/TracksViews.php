<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait TracksViews
{
    public function registerView(Model $model, string $title, string $url): void
    {
        $model->increment('views_count');

        if (! session('last_viewed')) {
            session(['last_viewed' => []]);
        }

        $viewed = collect(session('last_viewed'))
            ->reject(fn (array $item) => $item['type'] === $model::class && $item['id'] === $model->getKey())
            ->prepend([
                'type' => $model::class,
                'id' => $model->getKey(),
                'title' => $title,
                'url' => $url,
                'time' => now()->toIso8601String(),
            ])
            ->take(10)
            ->values()
            ->all();

        session(['last_viewed' => $viewed]);
    }
}
