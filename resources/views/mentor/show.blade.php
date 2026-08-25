@extends('layouts.app')

@section('title', $mentor->name.' — mentor — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex items-center gap-5">
            <span class="grid h-20 w-20 place-items-center rounded-3xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 text-3xl font-extrabold">{{ mb_substr($mentor->name, 0, 1) }}</span>
            <div>
                <h1 class="text-2xl font-extrabold">{{ $mentor->name }}</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $mentor->role }} · {{ $mentor->company ?? 'Mustaqil mentor' }}</p>
                <p class="mt-1 text-sm font-semibold text-amber-500">★ {{ $mentor->rating }} · {{ $mentor->experience_years }} yil tajriba</p>
            </div>
        </div>

        <div class="my-6 h-px bg-slate-100 dark:bg-slate-800"></div>
        <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $mentor->bio }}</p>

        <div class="mt-6 flex flex-wrap gap-1.5">
            @foreach ($mentor->skills ?? [] as $skill)
                <x-ui.badge tone="blue">{{ $skill }}</x-ui.badge>
            @endforeach
        </div>

        @auth
            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-700 dark:bg-slate-900">
                @if ($hasRequested)
                    <p class="font-semibold text-emerald-500">✅ So‘rov yuborilgan. Mentor javob beradi.</p>
                @else
                    <h2 class="font-bold">Mentorlik so‘rovi</h2>
                    <form method="POST" action="{{ route('mentor.request', $mentor) }}" class="mt-4 grid gap-4">
                        @csrf
                        <x-ui.textarea name="message" label="Savolingiz / maqsadingiz" placeholder="Nima bo‘yicha maslahat kerak?" required />
                        <x-ui.button>Yuborish</x-ui.button>
                    </form>
                @endif
            </div>
        @else
            <div class="mt-8 rounded-2xl bg-blue-600 p-6 text-center text-white">So‘rov uchun <a href="{{ route('login') }}" class="font-bold underline">kiring</a>.</div>
        @endauth
    </div>
</div>
@endsection
