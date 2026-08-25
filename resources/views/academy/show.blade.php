@extends('layouts.app')

@section('title', $course->title.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-ui.badge tone="violet">{{ $course->category }}</x-ui.badge>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight">{{ $course->title }}</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor }} ({{ $course->instructor_role }}) · {{ $course->duration }} · ★ {{ $course->rating }}</p>
            </div>
            <span class="text-2xl font-extrabold">{{ $course->isFree() ? 'Bepul' : number_format($course->price).' so‘m' }}</span>
        </div>

        <div class="my-8 h-px bg-slate-100 dark:bg-slate-800"></div>

        <section>
            <h2 class="font-bold">Kurs haqida</h2>
            <div class="mt-3 space-y-3 text-sm leading-7 text-slate-600 dark:text-slate-300">
                @foreach (preg_split('/\n{2,}/', $course->description) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </section>

        @auth
            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-900">
                @if ($enrolled)
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-emerald-500">✅ Kursga yozilgansiz — davom eting.</span>
                        <x-ui.button :href="route('my-courses.index')" variant="secondary">Kurslarim</x-ui.button>
                    </div>
                @else
                    <form method="POST" action="{{ route('academy.enroll', $course) }}" class="flex flex-wrap items-center justify-between gap-3">
                        @csrf
                        <p class="text-sm text-slate-500 dark:text-slate-400">Davomiylik va sertifikat bilan.</p>
                        <x-ui.button>Yozilish</x-ui.button>
                    </form>
                @endif
            </div>
        @else
            <div class="mt-8 rounded-2xl bg-blue-600 p-6 text-center text-white">
                <p class="font-semibold">Kursga yozilish uchun ro‘yxatdan o‘ting</p>
                <a href="{{ route('register') }}" class="mt-3 inline-block rounded-xl bg-white px-5 py-2 text-sm font-bold text-blue-700">Boshlash</a>
            </div>
        @endauth
    </div>
</div>
@endsection
