@extends('layouts.app')

@section('title', 'Referal dasturi — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <div class="reveal rounded-3xl bg-gradient-to-br from-blue-600 to-violet-600 p-10 text-center text-white">
        <p class="text-4xl">🎁</p>
        <h1 class="mt-3 text-3xl font-black">Do‘stingizni taklif qiling</h1>
        <p class="mx-auto mt-2 max-w-md text-sm text-blue-100">Har bir yangi foydalanuvchi profilni to‘ldirganda — ikkalangizga ham <strong>+50 GreenCoin</strong>.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-2">
            <code class="rounded-xl bg-white/10 px-4 py-2 font-mono text-sm">{{ $user->referral_code }}</code>
            <button onclick="navigator.clipboard.writeText('{{ $user->referralUrl() }}'); showToast('Nusxalandi')" class="rounded-xl bg-white px-4 py-2 text-sm font-bold text-blue-700">Havolani nusxalash</button>
        </div>
    </div>

    <x-ui.card title="Taklif qilganlaringiz ({{ $referrals->count() }})" class="mt-8">
        @forelse ($referrals as $referral)
            <div class="flex items-center justify-between py-2 text-sm">
                <div>
                    <p class="font-medium">{{ $referral->name }}</p>
                    <p class="text-xs text-slate-400">{{ $referral->created_at->format('d.m.Y') }}</p>
                </div>
                <span class="font-semibold text-emerald-500">{{ $referral->referral_rewarded ? '+50 🪙' : '⏳' }}</span>
            </div>
        @empty
            <x-ui.empty icon="👥" title="Hali taklif yo‘q" />
        @endforelse
    </x-ui.card>
</div>
@endsection
