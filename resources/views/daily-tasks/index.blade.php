@extends('layouts.app')

@section('title', 'Kunlik vazifalar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="✅ Kunlik vazifalar" description="Bugungi rejangizni belgilang." />

    <form method="POST" action="{{ route('daily-tasks.store') }}" class="mt-8 grid gap-3 rounded-2xl border border-dashed border-slate-300 p-5 sm:grid-cols-[1fr_140px_auto] dark:border-slate-700">
        @csrf
        <x-ui.input name="title" placeholder="Vazifa" required />
        <x-ui.select name="priority" :options="['low' => 'Past', 'medium' => 'O‘rta', 'high' => 'Yuqori']" />
        <x-ui.button variant="secondary" class="self-end">+ Qo‘shish</x-ui.button>
    </form>

    <div class="mt-8 grid gap-3">
        @forelse ($tasks as $task)
            <div class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('daily-tasks.toggle', $task) }}">
                        @csrf
                        <button class="grid h-6 w-6 place-items-center rounded-md border text-xs {{ $task->is_done ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 dark:border-slate-600' }}">
                            {{ $task->is_done ? '✓' : '' }}
                        </button>
                    </form>
                    <div>
                        <p class="{{ $task->is_done ? 'line-through text-slate-400' : 'font-semibold' }}">{{ $task->title }}</p>
                        <p class="text-xs text-slate-400">{{ $task->priority }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('daily-tasks.destroy', $task) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-slate-300 hover:text-rose-500">✕</button>
                </form>
            </div>
        @empty
            <x-ui.empty icon="✅" title="Bugungi vazifalar yo‘q" />
        @endforelse
    </div>
</div>
@endsection
