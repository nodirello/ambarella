<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#020617">
    <title>@yield('title', config('app.name').' — O‘zbekiston yoshlari platformasi')</title>
    <meta name="description" content="@yield('description', 'Bandlik, ekologiya, ta’lim, mentorlik — 20+ modul bitta platformada.')">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-extrabold tracking-tight text-lg">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-white shadow-lg shadow-blue-600/20">A</span>
                <span class="hidden sm:block">AMBARELLA</span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-medium text-slate-600 lg:flex dark:text-slate-300">
                <x-app.nav-link :href="route('jobs.index')">Ish</x-app.nav-link>
                <x-app.nav-link :href="route('eco.index')">Ekologiya</x-app.nav-link>
                <x-app.nav-link :href="route('academy.index')">Academy</x-app.nav-link>
                <x-app.nav-link :href="route('mentor.index')">Mentorlik</x-app.nav-link>
                <x-app.nav-link :href="route('forum.index')">Forum</x-app.nav-link>
                <x-app.nav-link :href="route('blog.index')">Blog</x-app.nav-link>
                @auth
                    <x-app.nav-link :href="route('favorites.index')">Sevimlilar</x-app.nav-link>
                @endauth
            </nav>

            <div class="flex items-center gap-2">
                <button data-theme-toggle type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-sm dark:border-slate-700" aria-label="Mavzu">🌙</button>
                <a href="{{ route('search') }}" class="hidden h-9 items-center gap-2 rounded-lg border border-slate-200 px-3 text-xs text-slate-500 sm:flex dark:border-slate-700 dark:text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    Qidirish <kbd class="rounded bg-slate-100 px-1.5 text-[10px] dark:bg-slate-800">⌘K</kbd>
                </a>

                @auth
                    <a href="{{ route('dashboard.index') }}" class="hidden rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 sm:block">Panel</a>
                    <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                        @csrf
                        <button class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium dark:border-slate-700">Chiqish</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium sm:block dark:border-slate-700">Kirish</a>
                    <a href="{{ route('register') }}" class="hidden rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 sm:block">Ro‘yxatdan o‘tish</a>
                @endauth

                <button data-menu-toggle type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 lg:hidden dark:border-slate-700" aria-label="Menyu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white px-4 py-3 lg:hidden dark:border-slate-800 dark:bg-slate-950">
            <nav class="grid gap-1 text-sm font-medium">
                <x-app.nav-link :href="route('jobs.index')" :mobile="true">Ish</x-app.nav-link>
                <x-app.nav-link :href="route('eco.index')" :mobile="true">Ekologiya</x-app.nav-link>
                <x-app.nav-link :href="route('academy.index')" :mobile="true">Academy</x-app.nav-link>
                <x-app.nav-link :href="route('mentor.index')" :mobile="true">Mentorlik</x-app.nav-link>
                <x-app.nav-link :href="route('forum.index')" :mobile="true">Forum</x-app.nav-link>
                <x-app.nav-link :href="route('blog.index')" :mobile="true">Blog</x-app.nav-link>
                @auth
                    <x-app.nav-link :href="route('dashboard.index')" :mobile="true">Shaxsiy panel</x-app.nav-link>
                @else
                    <x-app.nav-link :href="route('login')" :mobile="true">Kirish</x-app.nav-link>
                    <x-app.nav-link :href="route('register')" :mobile="true">Ro‘yxatdan o‘tish</x-app.nav-link>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('status'))
        <div data-flash="{{ session('status') }}" class="hidden"></div>
    @endif
    @if (session('success'))
        <div data-flash="{{ session('success') }}" class="hidden"></div>
    @endif
    @if ($errors->any())
        <div data-flash="{{ $errors->first() }}" data-flash-type="error" class="hidden"></div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 text-lg font-extrabold"><span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-blue-600 to-violet-600 text-white">A</span> AMBARELLA</div>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">O‘zbekiston yoshlari uchun yagona ekotizim.</p>
            </div>
            <div class="text-sm">
                <h4 class="font-semibold">Platforma</h4>
                <ul class="mt-3 grid gap-2 text-slate-500 dark:text-slate-400">
                    <li><a href="{{ route('jobs.index') }}">Bandlik</a></li>
                    <li><a href="{{ route('academy.index') }}">Academy</a></li>
                    <li><a href="{{ route('eco.index') }}">Ekologiya</a></li>
                    <li><a href="{{ route('startups.index') }}">Startuplar</a></li>
                </ul>
            </div>
            <div class="text-sm">
                <h4 class="font-semibold">Jamiyat</h4>
                <ul class="mt-3 grid gap-2 text-slate-500 dark:text-slate-400">
                    <li><a href="{{ route('forum.index') }}">Forum</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('events.index') }}">Tadbirlar</a></li>
                    <li><a href="{{ route('volunteer.index') }}">Ko‘ngillilik</a></li>
                </ul>
            </div>
            <div class="text-sm">
                <h4 class="font-semibold">Yordam</h4>
                <ul class="mt-3 grid gap-2 text-slate-500 dark:text-slate-400">
                    <li><a href="{{ route('about') }}">Biz haqimizda</a></li>
                    <li><a href="{{ route('faqs') }}">Ko‘p so‘raladigan savollar</a></li>
                    <li><a href="{{ route('contact') }}">Aloqa</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200 py-5 text-center text-xs text-slate-400 dark:border-slate-800">© {{ date('Y') }} AMBARELLA · SADAF DEV · Barcha huquqlar himoyalangan</div>
    </footer>
</body>
</html>
