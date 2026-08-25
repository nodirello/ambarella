<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Admin — AMBARELLA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">
    <aside class="hidden w-64 shrink-0 overflow-y-auto border-r border-slate-200 p-4 lg:block dark:border-slate-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2 font-extrabold">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-white">A</span>
            <span>AMBARELLA</span>
            <span class="rounded bg-blue-600 px-1.5 py-0.5 text-[10px] text-white">ADMIN</span>
        </a>

        @php
            $crudMenu = [
                ['news', '📰', 'Yangiliklar'],
                ['courses', '🎓', 'Kurslar'],
                ['eco-tasks', '🌱', 'Eko vazifalar'],
                ['mentors', '🧭', 'Mentorlar'],
                ['events', '📅', 'Tadbirlar'],
                ['startups', '🚀', 'Startuplar'],
                ['volunteer', '🤝', 'Ko‘ngillilik'],
                ['announcements', '📢', 'E’lonlar'],
                ['banners', '🖼️', 'Bannerlar'],
                ['faqs', '❓', 'FAQ'],
                ['stories', '⭐', 'Hikoyalar'],
            ];
        @endphp

        <nav class="mt-6 grid gap-1 text-sm font-medium">
            @foreach ([
                ['📊', 'Dashboard', 'admin.dashboard'],
                ['👥', 'Foydalanuvchilar', 'admin.users.index'],
                ['🛡️', 'Moderatsiya', 'admin.moderation.index'],
                ['💼', 'Ish e’lonlari', 'admin.jobs.index'],
                ['📄', 'Arizalar', 'admin.applications.index'],
                ['🪙', 'GreenCoin', 'admin.greencoin.index'],
                ['🤖', 'Telegram bot', 'admin.telegram.dashboard'],
                ['✉️', 'Murojaatlar', 'admin.contacts.index'],
                ['🗒️', 'Activity log', 'admin.activity-log.index'],
            ] as [$icon, $label, $route])
                <a href="{{ route($route) }}" @class(['rounded-xl px-3 py-2.5 transition hover:bg-slate-100 dark:hover:bg-slate-800', '!bg-blue-600/10 !text-blue-600 dark:!text-blue-400' => request()->routeIs($route)])>
                    <span class="mr-2">{{ $icon }}</span>{{ $label }}
                </a>
            @endforeach

            <p class="mt-4 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Kontent</p>
            @foreach ($crudMenu as [$resource, $icon, $label])
                <a href="{{ route('admin.crud.index', $resource) }}" @class(['rounded-xl px-3 py-2.5 transition hover:bg-slate-100 dark:hover:bg-slate-800', '!bg-blue-600/10 !text-blue-600 dark:!text-blue-400' => request()->route('resource') === $resource])>
                    <span class="mr-2">{{ $icon }}</span>{{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="flex-1">
        <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-slate-200 bg-white/80 px-6 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
            <h1 class="text-lg font-bold">@yield('admin-title', 'Boshqaruv paneli')</h1>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Saytga qaytish</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="rounded-lg border border-slate-200 px-3 py-1.5 dark:border-slate-700">Chiqish</button>
                </form>
            </div>
        </header>
        <main class="p-6">
            @if (session('success'))
                <div data-flash="{{ session('success') }}" class="hidden"></div>
            @endif
            @if ($errors->any())
                <div data-flash="{{ $errors->first() }}" data-flash-type="error" class="hidden"></div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
