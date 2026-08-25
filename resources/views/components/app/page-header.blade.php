@props(['title', 'description' => null])

<section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/40">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-2xl text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
        @endif
        <div class="mt-4">{{ $slot }}</div>
    </div>
</section>
