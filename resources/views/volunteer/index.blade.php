@extends('layouts.app')

@section('title', 'Ko‘ngillilik — AMBARELLA')

@section('content')
<x-app.page-header title="🤝 Ko‘ngillilik" description="Jamoa loyihalarida qatnashib, o‘z hissangizni qo‘shing.">
    <div class="grid max-w-lg grid-cols-3 gap-3">
        <x-ui.stat label="Loyihalar" :value="$stats['projects']" tone="blue" />
        <x-ui.stat label="Ishtirokchilar" :value="$stats['participants']" tone="green" />
        <x-ui.stat label="Soat" :value="$stats['hours']" tone="violet" />
    </div>
</x-app.page-header>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($projects as $project)
            <div class="reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <h2 class="font-bold">{{ $project->title }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $project->organization }} · {{ $project->city }}</p>
                <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $project->description }}</p>
                <div class="mt-auto pt-4">
                    @auth
                        <form method="POST" action="{{ route('volunteer.apply', $project) }}" class="grid gap-2">
                            @csrf
                            <x-ui.input name="message" placeholder="Qisqa xabar (ixtiyoriy)" />
                            <x-ui.button class="w-full" variant="secondary">Ariza yuborish</x-ui.button>
                        </form>
                    @else
                        <x-ui.button :href="route('login')" variant="secondary" class="w-full">Kirish</x-ui.button>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$projects" />
</div>
@endsection
