@extends('layouts.app')

@section('title', 'Blog — AMBARELLA')

@section('content')
<x-app.page-header title="📝 Blog" description="Jamiyat maqolalari — moderatsiyadan so‘ng e’lon qilinadi.">
    @auth
        <x-ui.button :href="route('blog.create')">+ Maqola yozish</x-ui.button>
    @endauth
</x-app.page-header>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('blog.show', $post) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge tone="violet">{{ $post->category ?? 'Blog' }}</x-ui.badge>
                    <span class="text-xs text-slate-400">{{ $post->published_at?->format('d.m.Y') }}</span>
                </div>
                <h2 class="mt-3 line-clamp-2 font-bold">{{ $post->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $post->content }}</p>
                <p class="mt-4 text-xs text-slate-400">{{ $post->author->name }} · 👁 {{ number_format($post->views_count) }}</p>
            </a>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$posts" />
</div>
@endsection
