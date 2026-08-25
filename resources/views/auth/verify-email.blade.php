@extends('layouts.app')

@section('title', 'Emailni tasdiqlash — AMBARELLA')

@section('content')
<div class="mx-auto max-w-md px-4 py-20 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 text-center dark:border-slate-800 dark:bg-slate-900/60">
        <div class="text-4xl">📧</div>
        <h1 class="mt-3 text-2xl font-extrabold">Emailingizni tasdiqlang</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Ro‘yxatdan o‘tishda ishlatilgan email manzilingizga yuborilgan havolani oching. Xat kelmagan bo‘lsa, qayta yuborish tugmasini bosing.</p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <x-ui.button class="w-full">Qayta yuborish</x-ui.button>
        </form>
    </div>
</div>
@endsection
