@extends('layouts.app')

@section('title', 'Ish e’lonlarim — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <div class="flex items-center justify-between">
        <x-app.page-header title="Ish e’lonlarim" />
        <x-ui.button :href="route('business.jobs.create')">+ Yangi</x-ui.button>
    </div>

    <div class="mt-6 grid gap-3">
        @forelse ($jobs as $job)
            <div class="reveal flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <p class="font-bold">{{ $job->title }}</p>
                    <p class="text-xs text-slate-400">{{ $job->created_at->format('d.m.Y') }} · {{ $job->applications_count ?? '' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.badge :tone="$job->is_active ? 'green' : 'rose'">{{ $job->is_active ? 'Faol' : 'Yopiq' }}</x-ui.badge>
                    <form method="POST" action="{{ route('business.jobs.toggle', $job) }}">
                        @csrf
                        <button class="text-xs font-semibold text-amber-500">{{ $job->is_active ? 'Yopish' : 'Ochish' }}</button>
                    </form>
                </div>
            </div>
        @empty
            <x-ui.empty icon="💼" title="Ish e’lonlari yo‘q">
                <x-ui.button :href="route('business.jobs.create')">Yaratish</x-ui.button>
            </x-ui.empty>
        @endforelse
    </div>
</div>
@endsection
