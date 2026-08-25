@extends('layouts.app')

@section('title', 'Eslatmalar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="📝 Eslatmalar" />

    <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <form method="POST" action="{{ route('notes.store') }}" class="grid gap-3 self-start rounded-2xl border border-dashed border-slate-300 p-5 dark:border-slate-700">
            @csrf
            <x-ui.input name="title" placeholder="Sarlavha" required />
            <x-ui.textarea name="content" placeholder="Eslatma matni…" rows="4" required />
            <x-ui.select name="color" :options="['slate' => 'Kulrang', 'blue' => 'Ko‘k', 'green' => 'Yashil', 'amber' => 'Sariq', 'rose' => 'Qizil']" />
            <x-ui.button variant="secondary">+ Qo‘shish</x-ui.button>
        </form>

        @forelse ($notes as $note)
            <div class="reveal self-start rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-bold">{{ $note->title }}</h3>
                    <div class="flex gap-1">
                        <form method="POST" action="{{ route('notes.pin', $note) }}">
                            @csrf
                            <button title="Pin" class="text-xs">{{ $note->is_pinned ? '📌' : '📍' }}</button>
                        </form>
                        <form method="POST" action="{{ route('notes.destroy', $note) }}">
                            @csrf
                            @method('DELETE')
                            <button title="O‘chirish" class="text-xs text-slate-400 hover:text-rose-500">✕</button>
                        </form>
                    </div>
                </div>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $note->content }}</p>
            </div>
        @empty
            <x-ui.empty icon="📝" title="Eslatmalar yo‘q" />
        @endforelse
    </div>
</div>
@endsection
