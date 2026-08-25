@extends('layouts.app')

@section('title', $product->name.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <a href="{{ route('farm.index') }}" class="text-sm text-slate-400">← Bozor</a>
    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-ui.badge tone="green">{{ $product->category ?? 'Mahsulot' }}</x-ui.badge>
                <h1 class="mt-3 text-2xl font-extrabold">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $product->profile->company_name }} · {{ $product->profile->city }}</p>
            </div>
            <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($product->price) }} so‘m</p>
        </div>
        <div class="my-6 h-px bg-slate-100 dark:bg-slate-800"></div>
        <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $product->description }}</p>
        <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-sm font-semibold">Sotuvchi bilan bog‘lanish</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">📞 {{ $product->profile->phone ?? '—' }} · 🌐 {{ $product->profile->website ?? '—' }}</p>
        </div>
    </div>
</div>
@endsection
