@extends('layouts.app')

@section('title', 'Qidiruv — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <form method="GET" action="{{ route('search') }}" class="flex gap-3">
        <x-ui.input name="q" :value="$term" placeholder="Ish, kurs, mentor, yangilik yoki forum mavzusi…" autofocus />
        <x-ui.button>Qidirish</x-ui.button>
    </form>

    @if ($term !== '')
        <div class="mt-8 grid gap-3">
            @forelse ($results as $result)
                <a href="{{ $result['url'] }}" class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                    <div>
                        <x-ui.badge>{{ $result['type'] }}</x-ui.badge>
                        <p class="mt-2 font-bold">{{ $result['title'] }}</p>
                        <p class="text-sm text-slate-400">{{ $result['meta'] }}</p>
                    </div>
                    <span class="text-slate-300">→</span>
                </a>
            @empty
                <x-ui.empty icon="🔍" title="Hech narsa topilmadi" message="Boshqa kalit so‘z bilan urinib ko‘ring" />
            @endforelse
        </div>
    @endif
</div>
@endsection
