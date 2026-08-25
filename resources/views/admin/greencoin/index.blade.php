@extends('layouts.admin')

@section('title', 'GreenCoin — Admin')
@section('admin-title', 'GreenCoin boshqaruvi')

@section('content')
<form method="GET" class="mb-6 max-w-xs">
    <x-ui.input name="search" placeholder="Ism yoki email qidirish…" />
</form>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[760px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">Foydalanuvchi</th><th class="px-4 py-3">🪙 Balans</th><th class="px-4 py-3">Tuzatish</th></tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-4 py-3 font-bold">{{ number_format($user->greencoin_balance) }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.greencoin.adjust', $user) }}" class="flex gap-2">
                            @csrf
                            <x-ui.input name="amount" type="number" placeholder="+/- miqdor" class="!w-28 !py-1.5" />
                            <x-ui.input name="reason" placeholder="Sabab" class="!w-56 !py-1.5" />
                            <button class="rounded-lg bg-blue-600 px-3 text-xs font-bold text-white">OK</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
