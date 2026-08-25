@extends('layouts.app')

@section('title', 'Ekologiya — AMBARELLA')

@section('content')
<x-app.page-header title="🌱 Ekologiya" description="Kichik qadamlar katta o‘zgarish. Har bir vazifa — GreenCoin mukofoti.">
    <div class="grid max-w-xl grid-cols-2 gap-3">
        <x-ui.stat label="Umumiy eko harakat" :value="number_format($stats['approved_actions'])" tone="green" />
        <x-ui.stat label="Bugun" :value="number_format($stats['today_actions'])" tone="violet" />
    </div>
</x-app.page-header>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($tasks as $task)
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-3">
                    <x-ui.badge tone="violet">{{ $task->category }}</x-ui.badge>
                    <span class="text-sm font-extrabold text-emerald-500">+{{ $task->reward }} 🪙</span>
                </div>
                <h3 class="mt-3 font-bold">{{ $task->title }}</h3>
                <p class="mt-1.5 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $task->description }}</p>

                @auth
                    <div class="mt-4">
                        @if (($myCompletions[$task->id] ?? null))
                            @if ($myCompletions[$task->id]->status === 'approved')
                                <span class="text-sm font-semibold text-emerald-500">✅ Bugun bajarildi (+{{ $task->reward }})</span>
                            @else
                                <span class="text-sm font-semibold text-amber-500">⏳ Moderatsiya kutilmoqda</span>
                            @endif
                        @else
                            <form method="POST" action="{{ route('eco.complete', $task) }}" class="grid gap-2">
                                @csrf
                                <x-ui.input name="proof_text" placeholder="Qanday bajarganingizni yozing…" />
                                <x-ui.button class="w-full" variant="secondary">Bajardim ✓</x-ui.button>
                            </form>
                        @endif
                    </div>
                @else
                    <a href="{{ route('login') }}" class="mt-4 inline-block text-sm font-semibold text-blue-600 dark:text-blue-400">Kirish va bajarish →</a>
                @endauth
            </div>
        @endforeach
    </div>
</div>
@endsection
