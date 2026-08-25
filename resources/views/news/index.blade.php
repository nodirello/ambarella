@extends('layouts.app')

@section('title', 'Yangiliklar — AMBARELLA')

@section('content')
<x-app.page-header title="📰 Yangiliklar" description="Platforma va jamiyat yangiliklari." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($news as $item)
            <a href="{{ route('news.show', $item) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge>{{ $item->category }}</x-ui.badge>
                    <span class="text-xs text-slate-400">{{ $item->published_at->format('d.m.Y') }}</span>
                </div>
                <h2 class="mt-3 line-clamp-2 font-bold">{{ $item->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $item->content }}</p>
                <p class="mt-4 text-xs text-slate-400">{{ $item->author_name }} · 👁 {{ number_format($item->views_count) }}</p>
            </a>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$news" />
</div>
@endsection
