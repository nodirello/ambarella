@extends('layouts.app')

@section('title', 'Sertifikatni tekshirish — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
    <x-app.page-header title="📜 Sertifikatni tekshirish" />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        @if ($certificate)
            <div class="grid place-items-center text-center">
                <div class="text-5xl">✅</div>
                <h2 class="mt-3 text-2xl font-black text-emerald-500">Haqiqiy sertifikat</h2>
                <p class="mt-4 text-lg font-bold">{{ $certificate['user']->name }}</p>
                <p class="text-sm text-slate-400">«{{ $certificate['course']->title }}» kursi tugatilgan</p>
                <p class="mt-3 font-mono text-xs text-slate-400">{{ $code }}</p>
            </div>
        @else
            <x-ui.empty icon="❌" title="Sertifikat topilmadi" message="Kod noto‘g‘ri yoki sertifikat hali berilmagan.">
                <p class="font-mono text-sm text-slate-400">{{ $code }}</p>
            </x-ui.empty>
        @endif
    </div>
</div>
@endsection
