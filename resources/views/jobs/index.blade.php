@extends('layouts.app')

@section('title', 'Ish e’lonlari — AMBARELLA')

@section('content')
<x-app.page-header title="Ish e’lonlari" description="Yangi imkoniyatlar — filtrlab toping va bir klikda ariza yuboring.">
    <form method="GET" class="grid gap-3 sm:grid-cols-[1fr_180px_180px_auto]">
        <x-ui.input name="search" :value="$filters['search'] ?? null" placeholder="Lavozim, kompaniya yoki shahar…" />
        <x-ui.select name="type" :options="['full' => 'To‘liq stavka', 'part' => 'Yarim stavka', 'remote' => 'Masofaviy', 'internship' => 'Amaliyot']" :value="$filters['type'] ?? null" placeholder="Barcha turlar" />
        <x-ui.input name="min_salary" type="number" :value="$filters['min_salary'] ?? null" placeholder="Min. maosh" />
        <x-ui.button type="submit" variant="secondary">Filtr</x-ui.button>
    </form>
</x-app.page-header>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-3">
        @forelse ($jobs as $job)
            <a href="{{ route('jobs.show', $job) }}" class="reveal group flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-blue-500/40 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 text-xl">{{ mb_substr($job->company, 0, 1) }}</span>
                    <div>
                        <h2 class="font-bold group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $job->title }}</h2>
                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $job->company }} · {{ $job->location }} · {{ $job->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <x-ui.badge tone="blue">{{ $job->type->label() }}</x-ui.badge>
                    @foreach (array_slice($job->tags ?? [], 0, 2) as $tag)
                        <x-ui.badge>{{ $tag }}</x-ui.badge>
                    @endforeach
                </div>
                <div class="text-right">
                    <p class="font-extrabold text-blue-600 dark:text-blue-400">{{ $job->salaryRange() }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $job->applications_count ?? '' }}</p>
                </div>
            </a>
        @empty
            <x-ui.empty icon="💼" title="Hech narsa topilmadi" message="Filtrni o‘zgartirib ko‘ring" />
        @endforelse
    </div>

    <x-ui.pagination :paginator="$jobs" />
</div>
@endsection
