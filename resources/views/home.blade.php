@extends('layouts.app')

@section('title', 'AMBARELLA — O‘zbekiston yoshlari platformasi')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blue-600/10 via-transparent to-transparent"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
        <div class="reveal">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-600 dark:text-blue-300">🇺🇿 O‘zbekiston yoshlari uchun</span>
            <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                Kelajagingizni <span class="bg-gradient-to-r from-blue-500 to-violet-500 bg-clip-text text-transparent">AMBARELLA</span> bilan quring
            </h1>
            <p class="mt-5 max-w-xl text-base text-slate-500 sm:text-lg dark:text-slate-400">
                Bandlik, ekologiya, ta’lim, mentorlik, startup va biznes — 20 dan ortiq modul bitta platformada. GreenCoin ishlang, bilim oling, imkoniyat yarating.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-ui.button :href="auth()->check() ? route('dashboard.index') : route('register')">Boshlash <span aria-hidden>→</span></x-ui.button>
                <x-ui.button :href="route('modules')" variant="secondary">Modullarni ko‘rish</x-ui.button>
            </div>
            <dl class="mt-10 grid max-w-md grid-cols-3 gap-4">
                <x-ui.stat label="Foydalanuvchilar" :value="number_format($stats['users'])" icon="👥" tone="blue" />
                <x-ui.stat label="Ish o‘rinlari" :value="number_format($stats['jobs'])" icon="💼" tone="green" />
                <x-ui.stat label="Kurslar" :value="number_format($stats['courses'])" icon="🎓" tone="violet" />
            </dl>
        </div>
        <div class="reveal hidden lg:block">
            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-blue-600/20 via-slate-900/40 to-violet-600/20 p-8 backdrop-blur">
                <div class="grid gap-4">
                    @forelse ($jobs->take(2) as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="rounded-2xl border border-white/10 bg-white/60 p-5 transition hover:scale-[1.02] dark:bg-slate-900/60">
                            <div class="flex items-center justify-between">
                                <p class="font-bold">{{ $job->title }}</p>
                                <x-ui.badge tone="green">{{ $job->type->label() }}</x-ui.badge>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $job->company }} · {{ $job->location }}</p>
                            <p class="mt-2 text-xs font-semibold text-blue-600 dark:text-blue-400">{{ $job->salaryRange() }}</p>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-white/10 p-6 text-sm text-slate-400">Ish e’lonlari tez orada.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Announcements --}}
@if ($announcements->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 sm:px-6">
        @foreach ($announcements as $announcement)
            <div class="reveal rounded-2xl border border-amber-400/30 bg-amber-500/10 px-5 py-4 text-sm">
                <strong>📢 {{ $announcement->title }}.</strong> {{ $announcement->body }}
            </div>
        @endforeach
    </section>
@endif

{{-- Modules grid --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
    <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-3xl font-extrabold tracking-tight">Platforma modullari</h2>
        <p class="mt-2 text-slate-500 dark:text-slate-400">Bitta akkaunt — barcha imkoniyatlar.</p>
    </div>
    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $modules = [
                ['💼', 'Bandlik', 'Ish e’lonlari va arizalar', route('jobs.index')],
                ['🌱', 'Ekologiya', 'Eko vazifalar va GreenCoin', route('eco.index')],
                ['🎓', 'Academy', 'Amaliy kurslar + sertifikat', route('academy.index')],
                ['🧭', 'Mentorlik', '1:1 mentorlik dasturlari', route('mentor.index')],
                ['🚀', 'Startuplar', 'Loyihangizni taqdim eting', route('startups.index')],
                ['🤝', 'Ko‘ngillilik', 'Jamoa loyihalarida qatnashing', route('volunteer.index')],
                ['📅', 'Tadbirlar', 'Festivallar va uchrashuvlar', route('events.index')],
                ['🏆', 'Musobaqalar', 'Haftalik challenge’lar', route('challenges.index')],
                ['💬', 'Forum', 'Jamiyat bilan muloqot', route('forum.index')],
                ['📝', 'Blog', 'Bilim va tajriba ulashing', route('blog.index')],
                ['🗳️', 'So‘rovnomalar', 'Fikringiz muhim', route('polls.index')],
                ['🧠', 'Psixologiya', 'O‘z-o‘zini baholash testlari', route('psychology.index')],
            ];
        @endphp
        @foreach ($modules as [$icon, $title, $desc, $url])
            <a href="{{ $url }}" class="reveal group rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:border-blue-500/40 hover:shadow-xl hover:shadow-blue-500/10 dark:border-slate-800 dark:bg-slate-900/60">
                <span class="text-3xl">{{ $icon }}</span>
                <h3 class="mt-3 font-bold group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</section>

{{-- Latest news --}}
<section class="border-y border-slate-200 bg-white py-20 dark:border-slate-800 dark:bg-slate-900/40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight">So‘nggi yangiliklar</h2>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Platforma hayoti va yangiliklari.</p>
            </div>
            <x-ui.button :href="route('news.index')" variant="ghost">Barchasi →</x-ui.button>
        </div>
        <div class="mt-8 grid gap-4 md:grid-cols-3">
            @foreach ($news as $item)
                <a href="{{ route('news.show', $item) }}" class="reveal rounded-2xl border border-slate-200 p-6 transition hover:border-blue-500/40 dark:border-slate-800">
                    <x-ui.badge>{{ $item->category }}</x-ui.badge>
                    <h3 class="mt-3 font-bold">{{ $item->title }}</h3>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $item->content }}</p>
                    <p class="mt-3 text-xs text-slate-400">{{ $item->published_at->diffForHumans() }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
    <div class="reveal rounded-3xl bg-gradient-to-br from-blue-600 to-violet-600 p-10 text-center text-white sm:p-16">
        <h2 class="text-3xl font-black sm:text-4xl">Bugun boshlang — ertaga natija ko‘ring</h2>
        <p class="mx-auto mt-3 max-w-xl text-blue-100">Ro‘yxatdan o‘tish 1 daqiqa. Profil to‘ldiring, birinchi eko-vazifani bajaring — +GreenCoin oling.</p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-700 shadow-lg">Ro‘yxatdan o‘tish</a>
            <a href="{{ route('login') }}" class="rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold hover:bg-white/10">Kirish</a>
        </div>
    </div>
</section>
@endsection
