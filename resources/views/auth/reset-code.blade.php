@extends('layouts.app')

@section('title', 'Kodni tasdiqlash — AMBARELLA')

@section('content')
<div class="mx-auto max-w-md px-4 py-20 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 text-center dark:border-slate-800 dark:bg-slate-900/60">
        <div class="text-4xl">🔐</div>
        <h1 class="mt-3 text-2xl font-extrabold">Kodni kiriting</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Yuborilgan 6 xonali kodni kiriting. Kod 10 daqiqa amal qiladi.</p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 grid gap-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <x-ui.input name="code" label="6 xonali kod" placeholder="123456" maxlength="6" required autofocus class="text-center text-2xl tracking-[0.5em]" />
            <x-ui.input name="password" label="Yangi parol" type="password" required hint="Kamida 8 ta belgi, katta harf va raqam" />
            <x-ui.input name="password_confirmation" label="Parolni takrorlang" type="password" required />
            <x-ui.button class="w-full">Parolni yangilash</x-ui.button>
        </form>
    </div>
</div>
@endsection
