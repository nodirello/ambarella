@extends('layouts.app')

@section('title', 'Ro‘yxatdan o‘tish — AMBARELLA')

@section('content')
<div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-800 dark:bg-slate-900/60">
        <h1 class="text-2xl font-extrabold">Hisob yaratish</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">1 daqiqada ro‘yxatdan o‘ting — barcha modullar ochiladi.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 grid gap-4">
            @csrf
            <x-ui.input name="name" label="To‘liq ism" placeholder="Jasur Toshmatov" required autofocus />
            <x-ui.input name="email" label="Email" type="email" placeholder="you@example.uz" required />
            <x-ui.input name="phone" label="Telefon (ixtiyoriy)" placeholder="+998 90 123 45 67" hint="Telegram orqali kirish uchun kerak" />
            <x-ui.input name="password" label="Parol" type="password" required hint="Kamida 8 ta belgi, 1 katta harf, 1 raqam" />
            <x-ui.input name="password_confirmation" label="Parolni takrorlang" type="password" required />
            <x-ui.button class="w-full">Ro‘yxatdan o‘tish</x-ui.button>
            <p class="text-center text-sm text-slate-400">Hisobingiz bormi? <a href="{{ route('login') }}" class="font-semibold text-blue-600 dark:text-blue-400">Kirish</a></p>
        </form>
    </div>
</div>
@endsection
