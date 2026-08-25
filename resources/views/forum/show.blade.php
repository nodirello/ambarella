@extends('layouts.app')

@section('title', $topic->title.' — Forum — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <a href="{{ route('forum.index') }}" class="text-sm text-slate-400">← Forum</a>

    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
        <h1 class="text-2xl font-extrabold">{{ $topic->title }}</h1>
        <p class="mt-1 text-sm text-slate-400">{{ $topic->author->name }} · {{ $topic->created_at->format('d.m.Y H:i') }}</p>
        <p class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $topic->body }}</p>
    </div>

    <div class="mt-8 grid gap-4">
        <h2 class="font-bold">{{ $topic->replies->count() }} ta javob</h2>
        @foreach ($topic->replies as $reply)
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold">{{ $reply->author->name }}</p>
                    <div class="flex items-center gap-2">
                        @if ($reply->is_best)<x-ui.badge tone="green">✅ Eng yaxshi</x-ui.badge>@endif
                        <span class="text-xs text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $reply->body }}</p>
                @auth
                    @if (! $topic->is_solved && ($topic->user_id === auth()->id() || auth()->user()->isStaff()))
                        <form method="POST" action="{{ route('forum.best', $reply) }}" class="mt-3">
                            @csrf
                            <button class="text-xs font-semibold text-blue-600 dark:text-blue-400">Eng yaxshi deb belgilash</button>
                        </form>
                    @endif
                @endauth
            </div>
        @endforeach
    </div>

    @auth
        <div class="reveal mt-8">
            <form method="POST" action="{{ route('forum.reply', $topic) }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                @csrf
                <x-ui.textarea name="body" label="Javob yozish" rows="4" required />
                <x-ui.button class="justify-self-start">Javob berish</x-ui.button>
            </form>
        </div>
    @endauth
</div>
@endsection
