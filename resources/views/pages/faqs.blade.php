@extends('layouts.app')

@section('title', 'Ko‘p so‘raladigan savollar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
    <x-app.page-header title="❓ Ko‘p so‘raladigan savollar" />

    <div class="mt-8 grid gap-4">
        @forelse ($faqs as $category => $items)
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-400">{{ $category }}</h2>
                <div class="mt-3 grid gap-3">
                    @foreach ($items as $faq)
                        <details class="reveal group rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                            <summary class="cursor-pointer list-none font-semibold marker:hidden">{{ $faq->question }}<span class="float-right text-slate-300 group-open:rotate-45">+</span></summary>
                            <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        @empty
            <x-ui.empty icon="❓" title="Savollar hali qo‘shilmagan" />
        @endforelse
    </div>
</div>
@endsection
