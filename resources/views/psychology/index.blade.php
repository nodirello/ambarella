@extends('layouts.app')

@section('title', 'Psixologiya — o‘z-o‘zini baholash — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🧠 Psixologiya" description="Ilmiy asoslangan o‘z-o‘zini baholash vositalari. Natijalar ma’lumot uchun — diagnostika emas." />

    <div class="mt-8 grid gap-4 md:grid-cols-2">
        @foreach ($tests as $slug => $test)
            <a href="{{ route('psychology.show', $slug) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-8 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <span class="text-3xl">🧭</span>
                <h2 class="mt-4 text-xl font-bold">{{ $test['title'] }}</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ count($test['questions']) }} savol · 1 daqiqa</p>
                <span class="mt-4 inline-block text-sm font-semibold text-blue-600 dark:text-blue-400">Boshlash →</span>
            </a>
        @endforeach
    </div>
</div>
@endsection
