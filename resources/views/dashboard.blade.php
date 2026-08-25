@extends('layouts.app')

@section('title', 'Dashboard — AMBARELLA')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Salom, {{ $user->name }} 👋</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $today }} · Davom eting!</p>
        </div>
        <x-ui.button :href="route('eco.index')">🌱 Eko vazifa</x-ui.button>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="GreenCoin balans" :value="number_format($stats['balance'])" icon="🪙" tone="green" />
        <x-ui.stat label="Seriya (kun)" :value="$stats['streak']" icon="🔥" tone="amber" />
        <x-ui.stat label="Arizalar" :value="$stats['applications']" icon="📄" tone="blue" />
        <x-ui.stat label="Bugungi vazifalar" :value="$stats['tasks_today']" icon="✅" tone="violet" />
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ui.card title="Tavsiya etilgan ishlar">
                <div class="grid gap-3">
                    @forelse ($recommended_jobs as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 transition hover:border-blue-500/40 dark:border-slate-800">
                            <div>
                                <p class="font-semibold">{{ $job->title }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $job->company }} · {{ $job->location }}</p>
                            </div>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ number_format($job->salary_min) }}+</span>
                        </a>
                    @empty
                        <x-ui.empty icon="💼" title="Hozircha ish yo‘q" />
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card title="Tez harakatlar">
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-ui.button :href="route('my-courses.index')" variant="secondary">🎓 Kurslarim</x-ui.button>
                    <x-ui.button :href="route('my-applications.index')" variant="secondary">📄 Arizalarim</x-ui.button>
                    <x-ui.button :href="route('notes.index')" variant="secondary">📝 Eslatmalar</x-ui.button>
                    <x-ui.button :href="route('referral.index')" variant="secondary">🎁 Referal dasturi</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="So‘nggi tranzaksiyalar">
                <div class="grid gap-3">
                    @forelse ($recent_transactions as $tx)
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium">{{ $tx->description }}</p>
                                <p class="text-xs text-slate-400">{{ $tx->created_at->diffForHumans() }}</p>
                            </div>
                            <span @class(['font-bold', 'text-emerald-500' => $tx->isCredit(), 'text-rose-500' => ! $tx->isCredit()])>
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </span>
                        </div>
                    @empty
                        <x-ui.empty icon="🪙" title="Tranzaksiya yo‘q" message="Birinchi eko-vazifani bajaring" />
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card title="Yaqin tadbirlar">
                <div class="grid gap-3">
                    @forelse ($upcoming_events as $event)
                        <a href="{{ route('events.show', $event) }}" class="rounded-xl border border-slate-200 p-4 text-sm dark:border-slate-800">
                            <p class="font-semibold">{{ $event->title }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $event->starts_at->translatedFormat('d M, H:i') }} · {{ $event->city }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400">Yaqin tadbir yo‘q.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
