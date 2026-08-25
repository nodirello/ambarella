@extends('layouts.app')

@section('title', 'Modullar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
    <div class="text-center">
        <h1 class="text-4xl font-black tracking-tight">20+ modul, bitta ekotizim</h1>
        <p class="mx-auto mt-3 max-w-xl text-slate-500 dark:text-slate-400">Bir akkaunt bilan barchasidan foydalaning.</p>
    </div>

    <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['💼', 'Bandlik', 'Ish e’lonlari, filtrlash, ariza tizimi', 'jobs.index'],
            ['🌱', 'Ekologiya', 'Kunlik eko vazifalar + GreenCoin', 'eco.index'],
            ['🎓', 'Academy', 'Kurslar, taraqqiyot, sertifikat', 'academy.index'],
            ['🧭', 'Mentorlik', '1:1 mentorship va vazifalar', 'mentor.index'],
            ['🪙', 'GreenCoin', 'Hamyon, transfer, reyting', 'greencoin.index'],
            ['🚀', 'Startuplar', 'Loyiha joylash va ovoz berish', 'startups.index'],
            ['🤝', 'Ko‘ngillilik', 'Loyihalarda ishtirok', 'volunteer.index'],
            ['📅', 'Tadbirlar', 'Festivallar va uchrashuvlar', 'events.index'],
            ['🏆', 'Musobaqalar', 'Haftalik challenge’lar', 'challenges.index'],
            ['💬', 'Forum', 'Mavzular va javoblar', 'forum.index'],
            ['📝', 'Blog', 'Maqolalar (moderatsiya bilan)', 'blog.index'],
            ['🗳️', 'So‘rovnomalar', 'Ovoz berish natijalari', 'polls.index'],
            ['🧠', 'Psixologiya', 'O‘z-o‘zini baholash', 'psychology.index'],
            ['✅', 'Kunlik vazifalar', 'Shaxsiy planer', 'daily-tasks.index'],
            ['🔥', 'Odatlar', 'Streak bilan odat qurish', 'habits.index'],
            ['📝', 'Eslatmalar', 'Tez yozuvlar', 'notes.index'],
            ['🎁', 'Referal', 'Do‘stlarini chaqir — 50+50 coin', 'referral.index'],
            ['🏅', 'Yutuqlar', 'Achievement tizimi', 'achievements.index'],
            ['🤵', 'Biznes paneli', 'Ish e’lonlari, arizalar, analitika', 'business.dashboard'],
            ['📱', 'Telegram Mini App', 'Telegram ichida to‘liq ilova', 'tg-app.index'],
        ] as [$icon, $title, $desc, $route])
            <a href="{{ $route !== 'tg-app.index' ? route($route) : route($route) }}" class="reveal group rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:border-blue-500/40 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $icon }}</span>
                    <h2 class="font-bold group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $title }}</h2>
                </div>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
