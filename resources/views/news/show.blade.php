@extends('layouts.app')

@section('title', $news->title.' — AMBARELLA')

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <a href="{{ route('news.index') }}" class="text-sm text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">← Barcha yangiliklar</a>
    <header class="reveal mt-4">
        <x-ui.badge>{{ $news->category }}</x-ui.badge>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight">{{ $news->title }}</h1>
        <p class="mt-3 text-sm text-slate-400">{{ $news->author_name }} · {{ $news->published_at->format('d.m.Y H:i') }} · 👁 {{ number_format($news->views_count) }}</p>
    </header>

    <div class="reveal mt-8 space-y-5 text-[15px] leading-8 text-slate-600 dark:text-slate-300">
        @foreach (preg_split('/\n{2,}/', $news->content) as $paragraph)
            <p>{{ $paragraph }}</p>
        @endforeach
    </div>

    {{-- Reactions --}}
    <div class="reveal mt-10 flex items-center gap-2">
        @foreach (['like', 'love', 'wow', 'savvy'] as $reaction)
            <button data-reaction data-type="news" data-id="{{ $news->id }}" data-reaction="{{ $reaction }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">{{ ['like' => '👍', 'love' => '❤️', 'wow' => '😮', 'savvy' => '🧠'][$reaction] }}</button>
        @endforeach
    </div>

    {{-- Comments --}}
    <section class="reveal mt-10">
        <h2 class="text-lg font-bold">Izohlar</h2>
        @auth
            <form method="POST" action="{{ route('comments.store') }}" class="mt-4 grid gap-3">
                @csrf
                <input type="hidden" name="commentable_type" value="news">
                <input type="hidden" name="commentable_id" value="{{ $news->id }}">
                <x-ui.textarea name="body" placeholder="Fikringizni yozing…" rows="3" required />
                <x-ui.button class="justify-self-start">Izoh qo‘shish</x-ui.button>
            </form>
        @endauth
        <div class="mt-6 grid gap-4">
            @forelse ($news->comments->where('is_approved', true) as $comment)
                <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-bold">{{ $comment->user->name }}</p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $comment->body }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Birinchi izohni qoldiring.</p>
            @endforelse
        </div>
    </section>
</article>
@endsection
