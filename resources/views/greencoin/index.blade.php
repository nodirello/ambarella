@extends('layouts.app')

@section('title', 'GreenCoin hamyon — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <div class="reveal grid gap-6 lg:grid-cols-[1fr_1.4fr]">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 p-8 text-white">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-200">Balans</p>
            <p class="mt-2 text-5xl font-black">{{ number_format($user->greencoin_balance) }} <span class="text-2xl">🪙</span></p>
            <p class="mt-4 text-sm text-emerald-100">Eko vazifalar, challenge’lar va referal orqali ishlang.</p>
            <div class="mt-6">
                <x-ui.button :href="route('eco.index')" class="!bg-white !text-emerald-700">🌱 Vazifalar</x-ui.button>
                <x-ui.button :href="route('greencoin.leaderboard')" variant="secondary" class="!mt-2 !border-white/30 !text-white">Reyting</x-ui.button>
            </div>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Transfer yuborish">
                <form method="POST" action="{{ route('greencoin.transfer') }}" class="grid gap-3 sm:grid-cols-[1fr_140px_auto]">
                    @csrf
                    <x-ui.input name="phone_or_email" placeholder="Email yoki telefon" required />
                    <x-ui.input name="amount" type="number" min="1" placeholder="Miqdor" required />
                    <x-ui.button class="self-end">Yuborish</x-ui.button>
                </form>
            </x-ui.card>

            <x-ui.card title="Tranzaksiyalar">
                <div class="grid gap-3">
                    @forelse ($transactions as $tx)
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium">{{ $tx->description }}</p>
                                <p class="text-xs text-slate-400">{{ $tx->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <span @class(['font-bold', 'text-emerald-500' => $tx->isCredit(), 'text-rose-500' => ! $tx->isCredit()])>
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </span>
                        </div>
                    @empty
                        <x-ui.empty icon="🪙" title="Hozircha tranzaksiya yo‘q" />
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
