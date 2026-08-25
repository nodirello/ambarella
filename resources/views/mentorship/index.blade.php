@extends('layouts.app')

@section('title', 'Mentorship — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <div class="flex items-center justify-between">
        <x-app.page-header title="🧭 Mentorship" description="Shogird–ustoz dasturlari va vazifalar." />
        <x-ui.button :href="route('mentorship.create')">+ Yangi</x-ui.button>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <section>
            <h2 class="font-bold">Shogird sifatida</h2>
            <div class="mt-3 grid gap-3">
                @forelse ($asStudent as $mentorship)
                    <x-ui.card :title="$mentorship->mentor->name" class="!p-5">
                        <x-ui.badge :tone="match ($mentorship->status) { 'active' => 'green', 'pending' => 'amber', 'completed' => 'blue', default => 'rose' }">{{ $mentorship->status }}</x-ui.badge>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($mentorship->goal, 120) }}</p>
                        @forelse ($mentorship->tasks as $task)
                            <div class="mt-3 flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700">
                                <span>{{ $task->title }}</span>
                                @if ($task->status !== 'done')
                                    <form method="POST" action="{{ route('mentorship.task.complete', $task) }}">
                                        @csrf
                                        <button class="text-xs font-semibold text-blue-600 dark:text-blue-400">Bajarildi</button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-500">✓</span>
                                @endif
                            </div>
                        @empty
                            <p class="mt-2 text-xs text-slate-400">Vazifalar hali qo‘shilmagan.</p>
                        @endforelse
                    </x-ui.card>
                @empty
                    <x-ui.empty icon="🧭" title="Mentorship yo‘q" message="Mentor tanlang" />
                @endforelse
            </div>
        </section>

        <section>
            <h2 class="font-bold">Mentor sifatida</h2>
            <div class="mt-3 grid gap-3">
                @forelse ($asMentor as $mentorship)
                    <x-ui.card :title="$mentorship->student->name">
                        <div class="flex items-center justify-between">
                            <x-ui.badge :tone="$mentorship->status === 'active' ? 'green' : 'amber'">{{ $mentorship->status }}</x-ui.badge>
                            @if ($mentorship->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('mentorship.accept', $mentorship) }}">
                                        @csrf
                                        <button class="text-xs font-bold text-emerald-500">Qabul qilish</button>
                                    </form>
                                    <form method="POST" action="{{ route('mentorship.reject', $mentorship) }}">
                                        @csrf
                                        <button class="text-xs font-bold text-rose-500">Rad etish</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('mentorship.task', $mentorship) }}" class="mt-3 flex gap-2">
                            @csrf
                            <input name="title" placeholder="Vazifa qo‘shish" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                            <button class="rounded-xl bg-blue-600 px-3 text-xs font-bold text-white">+</button>
                        </form>
                    </x-ui.card>
                @empty
                    <x-ui.empty icon="👥" title="Shogirdlar yo‘q" />
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
