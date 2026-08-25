@props(['tone' => 'slate'])

@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
        'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
        'violet' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300',
    ][$tone];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {$tones}"]) }}>{{ $slot }}</span>
