@extends('layouts.app')

@section('title', 'Yutuqlar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🏅 Yutuqlar" description="Qadamlaringiz uchun mukofotlar." />

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($achievements as $achievement)
            @php $earned = $earnedIds->contains($achievement->id); @endphp
            <div class="reveal rounded-2xl border p-6 text-center {{ $earned ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-slate-200 bg-white opacity-60 dark:border-slate-800 dark:bg-slate-900/60' }}">
                <div class="text-4xl">{{ ['flame' => '🔥', 'coin' => '🪙', 'briefcase' => '💼', 'leaf' => '🌱', 'star' => '⭐'][$achievement->icon] ?? '⭐' }}</div>
                <h3 class="mt-3 font-bold">{{ $achievement->title }}</h3>
                <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $achievement->description }}</p>
                <p class="mt-3 text-sm font-bold text-emerald-500">+{{ $achievement->reward }} 🪙</p>
                <p class="mt-2 text-xs {{ $earned ? 'text-emerald-500' : 'text-slate-400' }}">{{ $earned ? '✅ Qo‘lga kiritildi' : '🔒 Qulflangan' }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
