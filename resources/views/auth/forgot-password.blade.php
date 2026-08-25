@extends('layouts.app')

@section('title', 'Parolni tiklash — AMBARELLA')

@section('content')
<div class="mx-auto max-w-md px-4 py-20 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <h1 class="text-2xl font-extrabold">Parolni tiklash</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Emailingizga 6 xonali kod yuboramiz (Telegram ulangan bo‘lsa — Telegramga).</p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 grid gap-4">
            @csrf
            <x-ui.input name="email" label="Email" type="email" required autofocus />
            <x-ui.button class="w-full">Kod yuborish</x-ui.button>
        </form>
    </div>
</div>
@endsection
