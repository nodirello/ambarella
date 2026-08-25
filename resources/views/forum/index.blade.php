@extends('layouts.app')

@section('title', 'Forum — AMBARELLA')

@section('content')
<x-app.page-header title="💬 Forum" description="Savol bering, tajriba ulashing, jamiyatdan javob oling.">
    @auth
        <x-ui.button :href="route('forum.create')">+ Yangi mavzu</x-ui.button>
    @endauth
</x-app.page-header>

<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('forum.index') }}" class="rounded-full px-3 py-1 text-xs font-semibold {{ ! request('category') ? 'bg-blue-600 text-white' : 'border border-slate-200 dark:border-slate-700' }}">Barchasi</a>
        @foreach ($categories as $category)
            <a href="{{ route('forum.index', ['category' => $category]) }}" class="rounded-full px-3 py-1 text-xs font-semibold {{ request('category') === $category ? 'bg-blue-600 text-white' : 'border border-slate-200 dark:border-slate-700' }}">{{ $category }}</a>
        @endforeach
    </div>

    <div class="grid gap-3">
        @forelse ($topics as $topic)
            <a href="{{ route('forum.show', $topic) }}" class="reveal flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <div class="flex items-center gap-2">
                        @if ($topic->is_pinned)<span title="Mahkamlangan">📌</span>@endif
                        @if ($topic->is_solved)<span title="Yechilgan">✅</span>@endif
                        <h2 class="font-bold">{{ $topic->title }}</h2>
                    </div>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $topic->author->name }} · {{ $topic->created_at->diffForHumans() }} · {{ $topic->category }}</p>
                </div>
                <div class="text-right text-xs text-slate-400">
                    <p>{{ $topic->replies_count }} javob</p>
                    <p>👁 {{ number_format($topic->views_count) }}</p>
                </div>
            </a>
        @empty
            <x-ui.empty icon="💬" title="Hozircha mavzu yo‘q" />
        @endforelse
    </div>

    <x-ui.pagination :paginator="$topics" />
</div>
@endsection
