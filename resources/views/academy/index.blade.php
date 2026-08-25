@extends('layouts.app')

@section('title', 'Academy — kurslar — AMBARELLA')

@section('content')
<x-app.page-header title="🎓 Academy" description="Amaliy kurslar: video darslar, topshiriqlar va sertifikat." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($courses as $course)
            <div class="reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge tone="violet">{{ $course->category }}</x-ui.badge>
                    <span class="text-xs text-amber-500">★ {{ $course->rating }}</span>
                </div>
                <h3 class="mt-3 text-lg font-bold">{{ $course->title }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor }} · {{ $course->duration }}</p>
                <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $course->description }}</p>
                <div class="mt-auto flex items-center justify-between pt-5">
                    <span class="text-xl font-extrabold">
                        {{ $course->isFree() ? 'Bepul' : number_format($course->price).' so‘m' }}
                    </span>
                    <x-ui.button :href="route('academy.show', $course)" variant="secondary">Ko‘rish</x-ui.button>
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$courses" />
</div>
@endsection
