@extends('layouts.app')

@section('title', 'Kurslarim — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🎓 Kurslarim" description="Taraqqiyotni kuzating va sertifikat oling." />

    <div class="mt-8 grid gap-4">
        @forelse ($courses as $course)
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold">{{ $course->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor }}</p>
                    </div>
                    @if ($course->pivot->completed_at)
                        <x-ui.badge tone="green">✅ Tugatilgan</x-ui.badge>
                    @else
                        <x-ui.badge tone="blue">{{ $course->pivot->progress }}%</x-ui.badge>
                    @endif
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-violet-500 transition-all" style="width: {{ $course->pivot->progress }}%"></div>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    @if ($course->pivot->completed_at)
                        <x-ui.button :href="route('certificate.generate', $course)" variant="secondary">📜 Sertifikat</x-ui.button>
                    @else
                        <form method="POST" action="{{ route('my-courses.progress', $course) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="progress" min="0" max="100" value="{{ $course->pivot->progress }}" class="w-20 rounded-xl border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                            <x-ui.button variant="secondary">Yangilash</x-ui.button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <x-ui.empty icon="🎓" title="Hali kurs yo‘q" message="Academy’dan kurs tanlang">
                <x-ui.button :href="route('academy.index')">Kurslar</x-ui.button>
            </x-ui.empty>
        @endforelse
    </div>
</div>
@endsection
