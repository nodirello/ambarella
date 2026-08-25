@extends('layouts.app')

@section('title', 'Mentorlar — AMBARELLA')

@section('content')
<x-app.page-header title="🧭 Mentorlar" description="Tajribali mutaxassislardan 1:1 o‘rganing." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($mentors as $mentor)
            <div class="reveal rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center gap-4">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 text-xl font-extrabold">{{ mb_substr($mentor->name, 0, 1) }}</span>
                    <div>
                        <h3 class="font-bold">{{ $mentor->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $mentor->role }} · {{ $mentor->experience_years }} yil</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach ($mentor->skills ?? [] as $skill)
                        <x-ui.badge>{{ $skill }}</x-ui.badge>
                    @endforeach
                </div>
                <p class="mt-4 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $mentor->bio }}</p>
                <div class="mt-5 flex items-center justify-between">
                    <span class="text-sm font-bold text-amber-500">★ {{ $mentor->rating }}</span>
                    <x-ui.button :href="route('mentor.show', $mentor)" variant="secondary">So‘rov yuborish</x-ui.button>
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$mentors" />
</div>
@endsection
