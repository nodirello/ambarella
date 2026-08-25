@extends('layouts.app')

@section('title', $startup->name.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <a href="{{ route('startups.index') }}" class="text-sm text-slate-400">← Startuplar</a>
    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex gap-2">
                    <x-ui.badge>{{ $startup->category }}</x-ui.badge>
                    <x-ui.badge tone="violet">{{ $startup->stage }}</x-ui.badge>
                </div>
                <h1 class="mt-3 text-3xl font-extrabold">{{ $startup->name }}</h1>
                <p class="mt-1 text-sm text-slate-400">👥 {{ $startup->team_size }} kishi · {{ $startup->owner->name }}</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ number_format($startup->votes_count) }}</p>
                <p class="text-xs text-slate-400">ovoz</p>
            </div>
        </div>
        <div class="my-6 h-px bg-slate-100 dark:bg-slate-800"></div>
        <p class="whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $startup->description }}</p>
        @if ($startup->looking_for)
            <div class="mt-6 rounded-2xl border border-blue-500/30 bg-blue-500/5 p-4 text-sm">
                <strong>Qidirmoqda:</strong> {{ $startup->looking_for }}
            </div>
        @endif
        @auth
            <form method="POST" action="{{ route('startups.vote', $startup) }}" class="mt-8">
                @csrf
                <x-ui.button :variant="$voted ? 'secondary' : 'primary'">{{ $voted ? 'Ovozni olib tashlash' : '👏 Ovoz berish' }}</x-ui.button>
            </form>
        @endauth
    </div>
</div>
@endsection
