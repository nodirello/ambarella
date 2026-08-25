@props(['icon' => '📭', 'title' => 'Hech narsa topilmadi', 'message' => null])

<div class="grid place-items-center gap-3 rounded-2xl border border-dashed border-slate-300 py-16 text-center dark:border-slate-700">
    <div class="text-4xl">{{ $icon }}</div>
    <div>
        <p class="font-semibold">{{ $title }}</p>
        @if ($message)
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $message }}</p>
        @endif
    </div>
    {{ $slot }}
</div>
