@props(['title' => null, 'action' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/60']) }}>
    @if ($title || $action)
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <h3 class="font-semibold">{{ $title }}</h3>
            <div class="text-sm">{{ $action }}</div>
        </div>
    @endif
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</div>
