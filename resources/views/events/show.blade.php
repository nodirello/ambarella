@extends('layouts.app')

@section('title', $event->title.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <a href="{{ route('events.index') }}" class="text-sm text-slate-400">← Tadbirlar</a>
    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <x-ui.badge tone="blue">{{ $event->city ?? 'O‘zbekiston' }}</x-ui.badge>
        <h1 class="mt-3 text-3xl font-extrabold">{{ $event->title }}</h1>
        <div class="mt-3 grid gap-2 text-sm text-slate-500 dark:text-slate-400">
            <p>📅 {{ $event->starts_at->translatedFormat('d F Y, H:i') }}</p>
            <p>📍 {{ $event->venue ?? 'Manzil e’lon qilinadi' }}</p>
            <p>👥 {{ $event->registrations_count }}/{{ $event->capacity ?: 'cheksiz' }} · {{ $event->price > 0 ? number_format($event->price).' so‘m' : 'Bepul' }}</p>
        </div>
        <div class="my-6 h-px bg-slate-100 dark:bg-slate-800"></div>
        <p class="whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $event->description }}</p>

        @auth
            @if ($registered)
                <div class="mt-8 rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-5 text-sm font-semibold text-emerald-500">✅ Ro‘yxatdan o‘tgansiz. Chipta: {{ optional($event->registrations()->where('user_id', auth()->id())->first())->ticket_code }}</div>
            @else
                <form method="POST" action="{{ route('events.register', $event) }}" class="mt-8">
                    @csrf
                    <x-ui.button class="w-full sm:w-auto">Ro‘yxatdan o‘tish</x-ui.button>
                </form>
            @endif
        @else
            <div class="mt-8 rounded-2xl bg-blue-600 p-6 text-center text-white">Ro‘yxatdan o‘tish uchun <a href="{{ route('login') }}" class="font-bold underline">kiring</a>.</div>
        @endauth
    </div>
</div>
@endsection
