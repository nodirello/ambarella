@props(['href' => null, 'variant' => 'primary', 'type' => 'submit'])

@php
    $styles = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/20',
        'secondary' => 'border border-slate-200 hover:bg-slate-100 text-slate-700 dark:border-slate-700 dark:hover:bg-slate-800 dark:text-slate-200',
        'ghost' => 'text-slate-500 hover:text-slate-900 dark:hover:text-white',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
    ][$variant];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {$styles}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition {$styles}"]) }}>
        {{ $slot }}
    </button>
@endif
