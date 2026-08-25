@extends('layouts.app')

@section('title', 'Odatlar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🔥 Odatlar" description="Kunlik odatlar — seriyangizni uzmang." />

    <form method="POST" action="{{ route('habits.store') }}" class="mt-8 grid gap-3 rounded-2xl border border-dashed border-slate-300 p-5 sm:grid-cols-[1fr_auto] dark:border-slate-700">
        @csrf
        <x-ui.input name="title" placeholder="Yangi odat (masalan: 10 daqiqa kitob)" required />
        <x-ui.button variant="secondary" class="self-end">+ Qo‘shish</x-ui.button>
    </form>

    <div class="mt-8 grid gap-3">
        @forelse ($habits as $habit)
            <div class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <p class="font-bold">{{ $habit->title }}</p>
                    <p class="mt-1 text-xs text-slate-400">🔥 {{ $habit->current_streak }} kun · rekord {{ $habit->best_streak }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($habit->completedToday())
                        <span class="text-sm font-semibold text-emerald-500">✅ Bugun</span>
                    @else
                        <form method="POST" action="{{ route('habits.complete', $habit) }}">
                            @csrf
                            <x-ui.button variant="success">Bajardim</x-ui.button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('habits.destroy', $habit) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-slate-300 hover:text-rose-500">✕</button>
                    </form>
                </div>
            </div>
        @empty
            <x-ui.empty icon="🔥" title="Odat yo‘q" message="Kichik odat — katta natija" />
        @endforelse
    </div>
</div>
@endsection
