@extends('layouts.app')

@section('title', 'Tadbirlar — AMBARELLA')

@section('content')
<x-app.page-header title="📅 Tadbirlar" description="Festivallar, uchrashuvlar va ustaxonalar — ro‘yxatdan o‘ting." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($upcoming as $event)
            <div class="reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge tone="blue">{{ $event->city ?? 'O‘zbekiston' }}</x-ui.badge>
                    <span class="text-xs font-bold text-slate-400">{{ $event->starts_at->format('d M, H:i') }}</span>
                </div>
                <h2 class="mt-3 font-bold">{{ $event->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-slate-500 dark:text-slate-400">{{ $event->description }}</p>
                <div class="mt-auto flex items-center justify-between pt-5">
                    <span class="text-sm text-slate-400">{{ $event->price > 0 ? number_format($event->price).' so‘m' : 'Bepul' }} · {{ $event->registrations_count }}/{{ $event->capacity ?: '∞' }}</span>
                    <x-ui.button :href="route('events.show', $event)" variant="secondary">Batafsil</x-ui.button>
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$upcoming" />
</div>
@endsection
