@extends('layouts.app')

@section('title', $job->title.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <a href="{{ route('jobs.index') }}" class="text-sm text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">← Barcha e’lonlar</a>

    <div class="reveal mt-4 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 text-2xl">{{ mb_substr($job->company, 0, 1) }}</span>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ $job->title }}</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $job->company }} · {{ $job->location }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400">{{ $job->salaryRange() }}</p>
                <div class="mt-2 flex gap-2">
                    <x-ui.badge tone="blue">{{ $job->type->label() }}</x-ui.badge>
                    @if ($job->deadline)
                        <x-ui.badge tone="amber">Muddat: {{ $job->deadline->format('d.m.Y') }}</x-ui.badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="my-8 h-px bg-slate-100 dark:bg-slate-800"></div>

        <section>
            <h2 class="font-bold">Tavsif</h2>
            <div class="prose prose-slate mt-3 max-w-none text-sm leading-7 text-slate-600 dark:prose-invert dark:text-slate-300">{{ $job->description }}</div>
        </section>

        @if ($job->requirements)
            <section class="mt-8">
                <h2 class="font-bold">Talablar</h2>
                <p class="mt-2 text-sm leading-7 whitespace-pre-line text-slate-600 dark:text-slate-300">{{ $job->requirements }}</p>
            </section>
        @endif

        @if ($job->benefits)
            <section class="mt-8">
                <h2 class="font-bold">Imkoniyatlar</h2>
                <p class="mt-2 text-sm leading-7 whitespace-pre-line text-slate-600 dark:text-slate-300">{{ $job->benefits }}</p>
            </section>
        @endif

        @auth
            <div class="mt-10 rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-700 dark:bg-slate-900">
                @if ($hasApplied)
                    <div class="flex items-center gap-3 text-sm font-semibold text-emerald-500">✅ Arizangiz yuborilgan. Natijani kuting.</div>
                @else
                    <h2 class="font-bold">Ariza yuborish</h2>
                    <form method="POST" action="{{ route('jobs.apply', $job) }}" enctype="multipart/form-data" class="mt-4 grid gap-4">
                        @csrf
                        <x-ui.textarea name="cover_letter" label="Qisqa xat" placeholder="Nega aynan siz? (ixtiyoriy)" rows="3" />
                        <x-ui.input name="resume" label="Rezyume (PDF ga yaxshiroq)" type="file" accept=".pdf,.doc,.docx" />
                        <x-ui.button>Yuborish</x-ui.button>
                    </form>
                @endif
            </div>
        @else
            <div class="mt-10 rounded-2xl bg-blue-600 p-6 text-center text-white">
                <p class="font-semibold">Ariza yuborish uchun tizimga kiring</p>
                <div class="mt-4 flex justify-center gap-3">
                    <a href="{{ route('login') }}" class="rounded-xl bg-white px-5 py-2 text-sm font-bold text-blue-700">Kirish</a>
                    <a href="{{ route('register') }}" class="rounded-xl border border-white/40 px-5 py-2 text-sm font-semibold">Ro‘yxatdan o‘tish</a>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection
