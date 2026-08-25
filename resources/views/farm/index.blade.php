@extends('layouts.app')

@section('title', 'Qishloq mahsulotlari — AMBARELLA')

@section('content')
<x-app.page-header title="🌾 Qishloq mahsulotlari" description="Tasdiqlangan fermer va ishlab chiqaruvchilardan xarid qiling." />

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($products as $product)
            <a href="{{ route('farm.show', $product) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between">
                    <x-ui.badge tone="green">{{ $product->category ?? 'Mahsulot' }}</x-ui.badge>
                    <span class="text-xs text-slate-400">{{ $product->profile->company_name }}</span>
                </div>
                <h2 class="mt-3 font-bold">{{ $product->name }}</h2>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $product->description }}</p>
                <p class="mt-4 text-lg font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($product->price) }} so‘m</p>
            </a>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$products" />
</div>
@endsection
