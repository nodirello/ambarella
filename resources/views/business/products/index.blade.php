@extends('layouts.app')

@section('title', 'Mahsulotlar — Biznes — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Mahsulotlar" description="Mahsulotlar «Qishloq bozori» modulida namoyish etiladi." />

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_1.4fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
            <form method="POST" action="{{ route('business.products.store') }}" enctype="multipart/form-data" class="grid gap-4">
                @csrf
                <x-ui.input name="name" label="Nomi" required />
                <x-ui.input name="category" label="Kategoriya" placeholder="Meva, sabzavot…" />
                <x-ui.input name="price" label="Narx (so‘m)" type="number" required />
                <x-ui.textarea name="description" label="Tavsif" rows="3" />
                <x-ui.input name="image" label="Rasm" type="file" accept="image/*" />
                <x-ui.button>Qo‘shish</x-ui.button>
            </form>
        </div>

        <div class="grid gap-3">
            @forelse ($products as $product)
                <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                    <div>
                        <p class="font-bold">{{ $product->name }}</p>
                        <p class="text-xs text-slate-400">{{ $product->category }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($product->price) }}</span>
                        <x-ui.badge :tone="$product->is_active ? 'green' : 'rose'">{{ $product->is_active ? 'Faol' : 'Yopiq' }}</x-ui.badge>
                    </div>
                </div>
            @empty
                <x-ui.empty icon="🌾" title="Mahsulotlar yo‘q" />
            @endforelse
        </div>
    </div>
</div>
@endsection
