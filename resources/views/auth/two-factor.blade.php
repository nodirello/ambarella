@extends('layouts.app')

@section('title', '2FA — AMBARELLA')

@section('content')
<div class="mx-auto max-w-md px-4 py-20 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 text-center dark:border-slate-800 dark:bg-slate-900/60">
        <div class="text-4xl">🛡️</div>
        <h1 class="mt-3 text-2xl font-extrabold">Ikki bosqichli tasdiqlash</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Authenticator ilovangizdagi 6 xonali kodni kiriting. Tarmoqdan uzilgan bo‘lsangiz, zaxira kodlaridan foydalaning.</p>

        <form method="POST" action="{{ route('2fa.verify') }}" class="mt-6 grid gap-4">
            @csrf
            <x-ui.input name="code" label="TOTP kod" placeholder="000000" maxlength="6" required autofocus class="text-center text-2xl tracking-[0.5em]" />
            <x-ui.button class="w-full">Tasdiqlash</x-ui.button>
        </form>

        <a href="{{ route('2fa.recovery') }}" class="mt-4 inline-block text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Zaxira kod bilan kirish</a>
    </div>
</div>
@endsection
