@extends('layouts.app')

@section('title', 'Sevimlilar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="⭐ Sevimlilar" description="Saqlangan ishlar, kurslar va yangiliklar." />

    <div class="mt-8 grid gap-3">
        @forelse ($favorites as $favorite)
            @if ($favorite->favoritable)
                <a href="{{ route('home') }}" class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60" onclick="return false">
                    <div>
                        <p class="text-xs text-slate-400">{{ basename(str_replace('\\', '/', $favorite->favoritable_type)) }}</p>
                        <p class="mt-1 font-bold">{{ $favorite->favoritable->title ?? $favorite->favoritable->name }}</p>
                    </div>
                    <span class="text-slate-300">☆</span>
                </a>
            @endif
        @empty
            <x-ui.empty icon="⭐" title="Sevimlilar yo‘q" message="Ishlar, kurslar va yangiliklar yonidagi tugma orqali saqlang." />
        @endforelse
    </div>

    <x-ui.pagination :paginator="$favorites" />
</div>
@endsection
