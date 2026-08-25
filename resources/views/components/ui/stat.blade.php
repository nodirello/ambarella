@props(['label', 'value', 'icon' => null, 'tone' => 'blue'])

@php
    $tones = [
        'blue' => 'from-blue-600/15 to-blue-600/5 text-blue-600 dark:text-blue-400',
        'green' => 'from-emerald-600/15 to-emerald-600/5 text-emerald-600 dark:text-emerald-400',
        'violet' => 'from-violet-600/15 to-violet-600/5 text-violet-600 dark:text-violet-400',
        'amber' => 'from-amber-600/15 to-amber-600/5 text-amber-600 dark:text-amber-400',
        'rose' => 'from-rose-600/15 to-rose-600/5 text-rose-600 dark:text-rose-400',
        'slate' => 'from-slate-600/15 to-slate-600/5 text-slate-600 dark:text-slate-400',
    ][$tone] ?? ['from-slate-600/15 to-slate-600/5 text-slate-600 dark:text-slate-400'];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-2xl font-extrabold tracking-tight">{{ $value }}</p>
        </div>
        @if ($icon)
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br text-lg shadow {{ $tones }}">{{ $icon }}</span>
        @endif
    </div>
</div>
