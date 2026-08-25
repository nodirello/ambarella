@extends('layouts.app')

@section('title', $poll->title.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
    <a href="{{ route('polls.index') }}" class="text-sm text-slate-400">← So‘rovnomalar</a>
    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <h1 class="text-2xl font-extrabold">{{ $poll->title }}</h1>
        <p class="mt-1 text-sm text-slate-400">{{ $poll->votes->count() }} ovoz</p>

        @php $totalVotes = max(1, $poll->votes->count()); @endphp

        <div class="mt-6 grid gap-3">
            @foreach ($poll->options as $option)
                @php $count = $option->votes->count(); $pct = round($count / $totalVotes * 100); @endphp
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <div class="absolute inset-y-0 left-0 bg-blue-500/10" style="width: {{ $pct }}%"></div>
                    <div class="relative flex items-center justify-between text-sm">
                        <span class="font-medium">{{ $option->label }}</span>
                        <strong>{{ $pct }}% <span class="text-xs font-normal text-slate-400">({{ $count }})</span></strong>
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            @unless ($myVote)
                <form method="POST" action="{{ route('polls.vote', $poll) }}" class="mt-6 grid gap-3">
                    @csrf
                    <x-ui.select name="option_id" label="Ovozingiz" :options="$poll->options->pluck('label', 'id')->all()" placeholder="Variant tanlang" required />
                    <x-ui.button>Ovoz berish</x-ui.button>
                </form>
            @else
                <p class="mt-6 text-sm font-semibold text-emerald-500">✅ Ovoziingiz qabul qilingan.</p>
            @endunless
        @endauth
    </div>
</div>
@endsection
