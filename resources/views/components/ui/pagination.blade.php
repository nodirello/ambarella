@props(['paginator', 'label' => null])

@if ($paginator->hasPages())
    <nav class="mt-8 flex items-center justify-between gap-4 text-sm">
        <div class="text-xs text-slate-400">
            {{ $label ?? $paginator->firstItem() . '–' . $paginator->lastItem() . ' / ' . $paginator->total() }}
        </div>
        <div class="flex items-center gap-1.5">
            {{ $paginator->onFirstPage()
                ? '<span class="pointer-events-none rounded-lg border border-slate-200 px-3 py-1.5 text-slate-300 dark:border-slate-800">←</span>'
                : '<a class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" href="'.$paginator->previousPageUrl().'">←</a>' }}
            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="rounded-lg bg-blue-600 px-3 py-1.5 font-semibold text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">{{ $page }}</a>
                @endif
            @endforeach
            {{ $paginator->hasMorePages()
                ? '<a class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" href="'.$paginator->nextPageUrl().'">→</a>'
                : '<span class="pointer-events-none rounded-lg border border-slate-200 px-3 py-1.5 text-slate-300 dark:border-slate-800">→</span>' }}
        </div>
    </nav>
@endif
