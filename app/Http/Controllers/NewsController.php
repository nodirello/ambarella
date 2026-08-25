<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\News;
use App\Traits\TracksViews;
use Illuminate\View\View;

class NewsController extends Controller
{
    use TracksViews;

    public function index(): View
    {
        return view('news.index', [
            'news' => News::published()->latest()->paginate(12),
        ]);
    }

    public function show(News $news): View
    {
        abort_unless($news->is_published, 404);

        $this->registerView($news, $news->title, route('news.show', $news));

        return view('news.show', [
            'news' => $news->load('comments.user'),
        ]);
    }
}
