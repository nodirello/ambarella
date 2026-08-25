@extends('layouts.app')

@section('title', $test['title'].' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <a href="{{ route('psychology.index') }}" class="text-sm text-slate-400">← Barcha testlar</a>

    @if (session('test_result'))
        <div class="reveal mt-6 rounded-3xl border border-blue-500/30 bg-blue-500/5 p-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-500">Natija</p>
            <p class="mt-2 text-4xl font-black">{{ session('test_result')->score }}/{{ session('test_result')->max }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ session('test_result')->text }}</p>
            <p class="mt-4 text-xs text-slate-400">Bu natija faqat o‘z-o‘zini baholash uchun. Jiddiy muammolarda psixolog bilan bog‘laning.</p>
        </div>
    @endif

    <div class="reveal mt-6 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <h1 class="text-2xl font-extrabold">{{ $test['title'] }}</h1>
        <form method="POST" action="{{ route('psychology.submit', $slug) }}" class="mt-6 grid gap-6">
            @csrf
            @foreach ($test['questions'] as $index => $question)
                <fieldset>
                    <legend class="text-sm font-semibold">{{ $index + 1 }}. {{ $question['text'] }}</legend>
                    <div class="mt-2 grid gap-2">
                        @foreach ($question['options'] as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5 text-sm has-checked:border-blue-500 has-checked:bg-blue-500/5 dark:border-slate-700">
                                <input type="radio" name="{{ $question['key'] }}" value="{{ $value }}" class="rounded-full border-slate-300 text-blue-600 focus:ring-blue-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
            <x-ui.button>Natijani ko‘rish</x-ui.button>
        </form>
    </div>
</div>
@endsection
