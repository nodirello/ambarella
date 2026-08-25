@extends('layouts.app')

@section('title', 'Kirish — AMBARELLA')

@section('content')
<div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2">
    <div class="reveal self-center">
        <h1 class="text-3xl font-extrabold tracking-tight">Xush kelibsiz! 👋</h1>
        <p class="mt-3 max-w-md text-slate-500 dark:text-slate-400">Hisobingizga kiring va imkoniyatlardan foydalanishni davom eting. GreenCoin balansingiz, arizalaringiz va kurslaringiz — hammasi joyida.</p>
        <div class="mt-8 space-y-3 text-sm">
            <div class="flex gap-3 rounded-2xl border border-slate-200 p-4 dark:border-slate-800"><span>💼</span><p><strong>Ish toping</strong> — 100+ e’lon, filtr va ariza tizimi.</p></div>
            <div class="flex gap-3 rounded-2xl border border-slate-200 p-4 dark:border-slate-800"><span>🌱</span><p><strong>GreenCoin ishlang</strong> — eko vazifalar uchun mukofot.</p></div>
        </div>
    </div>

    <div class="reveal">
        <div class="mx-auto max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-800 dark:bg-slate-900/60">
            <h2 class="text-xl font-bold">Email bilan kirish</h2>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 grid gap-4">
                @csrf
                <x-ui.input name="email" label="Email" type="email" placeholder="you@example.uz" required autofocus />
                <x-ui.input name="password" label="Parol" type="password" required />
                <label class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"> Eslab qolish
                </label>
                <x-ui.button>Kirish</x-ui.button>
            </form>

            <div class="my-6 flex items-center gap-3 text-xs text-slate-400"><span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>yoki<span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span></div>

            <div class="grid gap-3">
                <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="AmbarellaBot" data-size="large" data-onauth="telegramCallback(user)" data-request-access="write"></script>
                <a href="{{ route('register') }}" class="text-center text-sm font-semibold text-blue-600 dark:text-blue-400">Hisob yo‘qmi? Ro‘yxatdan o‘tish</a>
                <a href="{{ route('password.request') }}" class="text-center text-sm text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Parolni unutdingizmi?</a>
            </div>
        </div>
    </div>
</div>

<script>
window.telegramCallback = function (user) {
    const params = new URLSearchParams(user);
    window.location.href = '{{ route('telegram.callback') }}?' + params.toString();
};
</script>
@endsection
