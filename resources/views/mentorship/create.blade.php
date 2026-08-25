@extends('layouts.app')

@section('title', 'Yangi mentorship — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Yangi mentorship" description="Mentor tanlang va maqsadingizni yozing." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('mentorship.store') }}" class="grid gap-5">
            @csrf
            <x-ui.select name="mentor_id" label="Mentor" :options="$mentors->pluck('name', 'id')->all()" placeholder="Tanlang" required />
            <x-ui.textarea name="goal" label="Maqsad" placeholder="Qanday yo‘nalishda o‘sishni xohlaysiz?" rows="4" required />
            <x-ui.button>Yuborish</x-ui.button>
        </form>
    </div>
</div>
@endsection
