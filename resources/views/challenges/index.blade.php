@extends('layouts.app')

@section('title', 'Musobaqalar — AMBARELLA')

@section('content')
<x-app.page-header title="🏆 Musobaqalar" description="Challenge’larda qatnashing — GreenCoin ishlang." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($challenges as $challenge)
            <div class="reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge tone="violet">{{ $challenge->category }}</x-ui.badge>
                    <span class="font-extrabold text-emerald-500">+{{ $challenge->reward }} 🪙</span>
                </div>
                <h2 class="mt-3 font-bold">{{ $challenge->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-slate-500 dark:text-slate-400">{{ $challenge->description }}</p>
                <p class="mt-3 text-xs text-slate-400">
                    {{ $challenge->starts_at?->format('d.m') }} — {{ $challenge->ends_at?->format('d.m') }}
                </p>
                <div class="mt-auto pt-4">
                    @auth
                        @if (in_array($challenge->id, $joinedIds, true))
                            <span class="text-sm font-semibold text-emerald-500">✅ Qatnashyapsiz</span>
                        @else
                            <form method="POST" action="{{ route('challenges.join', $challenge) }}">
                                @csrf
                                <x-ui.button class="w-full">Qatnashish</x-ui.button>
                            </form>
                        @endif
                    @else
                        <x-ui.button :href="route('login')" variant="secondary" class="w-full">Kirish</x-ui.button>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$challenges" />
</div>
@endsection
