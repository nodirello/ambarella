@extends('layouts.app')

@section('title', 'Sertifikat — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <div class="reveal rounded-3xl border-4 border-blue-600/30 bg-white p-10 text-center dark:bg-slate-900/60">
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-blue-500">AMBARELLA Academy</p>
        <h1 class="mt-3 text-2xl font-black">Sertifikat</h1>
        <p class="mt-6 text-sm text-slate-400">Ushbu sertifikat</p>
        <p class="mt-2 text-3xl font-black">{{ $user->name }}</p>
        <p class="mt-2 text-sm text-slate-400">kursini muvaffaqiyatli tugatganini tasdiqlaydi</p>
        <p class="mt-3 text-xl font-bold">{{ $course->title }}</p>

        <div class="mt-10 flex items-end justify-between text-left text-xs text-slate-400">
            <div>
                <div class="h-px w-40 bg-slate-300 dark:bg-slate-600"></div>
                <p class="mt-2">{{ $course->instructor }}</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-sm text-slate-500">{{ $code }}</p>
                <p class="mt-3">Tekshirish: <a href="{{ route('certificate.verify', $code) }}" class="text-blue-500 underline">{{ route('certificate.verify', $code) }}</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
