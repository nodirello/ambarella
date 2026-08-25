<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Models\BlogPost;
use App\Services\ImageService;
use App\Traits\LogsActivity;
use App\Traits\TracksViews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    use LogsActivity;
    use TracksViews;

    public function index(): View
    {
        return view('blog.index', [
            'posts' => BlogPost::published()->with('author')->latest()->paginate(12),
        ]);
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->status === ModerationStatus::Approved, 404);

        $this->registerView($post, $post->title, route('blog.show', $post));

        return view('blog.show', [
            'post' => $post->load('author', 'comments.user'),
        ]);
    }

    public function create(): View
    {
        return view('blog.create');
    }

    public function store(Request $request, ImageService $images): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'min:50', 'max:50000'],
            'category' => ['nullable', 'string', 'max:60'],
            'tags' => ['nullable', 'array', 'max:6'],
            'tags.*' => ['string', 'max:30'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $post = BlogPost::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'] ?? null,
            'tags' => $data['tags'] ?? [],
            'cover_image' => $images->upload($request->file('cover_image'), 'blog', 1600),
            'status' => ModerationStatus::Pending,
        ]);

        $this->logActivity('blog_create', "Yangi maqola: {$post->title}");

        return redirect()->route('blog.index')->with('success', 'Maqolangiz moderatsiyaga yuborildi.');
    }
}
