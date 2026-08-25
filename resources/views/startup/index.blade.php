@extends('layouts.app')

@section('title', 'Startuplar — AMBARELLA')

@section('content')
<x-app.page-header title="🚀 Startuplar" description="Loyihangizni taqdim eting, ovoz oling, hamkor toping.">
    @auth
        <x-ui.button :href="route('startups.create')">+ Loyiha joylash</x-ui.button>
    @endauth
</x-app.page-header>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($startups as $startup)
            <a href="{{ route('startups.show', $startup) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge>{{ $startup->category }}</x-ui.badge>
                    <span class="text-xs text-slate-400">{{ $startup->stage }}</span>
                </div>
                <h2 class="mt-3 font-bold">{{ $startup->name }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-slate-500 dark:text-slate-400">{{ $startup->description }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">👏 {{ number_format($startup->votes_count) }} ovoz</span>
                    <span class="text-xs text-slate-400">👥 {{ $startup->team_size }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$startups" />
</div>
@endsection
