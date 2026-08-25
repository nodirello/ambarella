<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumTopic;
use App\Models\Job;
use App\Models\Mentor;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);
        $results = collect();

        if (mb_strlen($term) >= 2) {
            $like = "%{$term}%";

            $results = collect()
                ->merge(Job::active()->where(fn ($q) => $q
                    ->where('title', 'like', $like)
                    ->orWhere('company', 'like', $like)
                    ->orWhere('location', 'like', $like))->limit(5)->get()->map(fn ($m) => [
                        'type' => 'Ish', 'title' => $m->title, 'url' => route('jobs.show', $m), 'meta' => $m->company,
                    ]))
                ->merge(Course::published()->where(fn ($q) => $q
                    ->where('title', 'like', $like)
                    ->orWhere('instructor', 'like', $like)
                    ->orWhere('category', 'like', $like))->limit(5)->get()->map(fn ($m) => [
                        'type' => 'Kurs', 'title' => $m->title, 'url' => route('academy.show', $m), 'meta' => $m->category,
                    ]))
                ->merge(Mentor::available()->where(fn ($q) => $q
                    ->where('name', 'like', $like)
                    ->orWhere('role', 'like', $like)
                    ->orWhere('company', 'like', $like))->limit(5)->get()->map(fn ($m) => [
                        'type' => 'Mentor', 'title' => $m->name, 'url' => route('mentor.show', $m), 'meta' => $m->role,
                    ]))
                ->merge(News::published()->where('title', 'like', $like)->limit(5)->get()->map(fn ($m) => [
                    'type' => 'Yangilik', 'title' => $m->title, 'url' => route('news.show', $m), 'meta' => $m->category,
                ]))
                ->merge(ForumTopic::where('title', 'like', $like)->limit(5)->get()->map(fn ($m) => [
                    'type' => 'Forum', 'title' => $m->title, 'url' => route('forum.show', $m), 'meta' => $m->category,
                ]));
        }

        return view('search.index', [
            'term' => $term,
            'results' => $results,
        ]);
    }
}
