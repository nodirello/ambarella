@extends('layouts.app')

@section('title', 'So‘nggi ko‘rilganlar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🕘 So‘nggi ko‘rilganlar" description="Oxirgi 10 ta ko‘rilgan kontent." />

    <div class="mt-8 grid gap-3">
        @forelse ($items as $item)
            <a href="{{ $item['url'] }}" class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <p class="font-bold">{{ $item['title'] }}</p>
                    <p class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($item['time'])->diffForHumans() }}</p>
                </div>
                <span class="text-slate-300">→</span>
            </a>
        @empty
            <x-ui.empty icon="🕘" title="Hali hech narsa ko‘rilmagan" />
        @endforelse
    </div>
</div>
@endsection
