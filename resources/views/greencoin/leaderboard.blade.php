@extends('layouts.app')

@section('title', 'Reyting — GreenCoin — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🏆 GreenCoin reytingi" description="Eng faol foydalanuvchilar." />

    <div class="mt-6 grid gap-3">
        @foreach ($topUsers as $index => $user)
            <div class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center gap-4">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 font-extrabold">
                        {{ $index < 3 ? ['🥇', '🥈', '🥉'][$index] : $index + 1 }}
                    </span>
                    <div>
                        <p class="font-bold">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">🔥 {{ $user->login_streak }} kun seriya</p>
                    </div>
                </div>
                <span class="font-extrabold text-emerald-500">{{ number_format($user->greencoin_balance) }} 🪙</span>
            </div>
        @endforeach
    </div>

    @if ($me && ! $topUsers->contains('id', $me->id))
        <div class="mt-4 rounded-2xl border border-blue-500/30 bg-blue-500/5 p-4 text-sm">
            Sizning o‘rningiz: <strong>{{ $topUsers->count() + 1 }}+</strong> ({{ number_format($me->greencoin_balance) }} 🪙)
        </div>
    @endif
</div>
@endsection
