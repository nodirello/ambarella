@extends('layouts.admin')

@section('title', ucfirst($resource).' — Admin')
@section('admin-title', ucfirst(str_replace('-', ' ', $resource)))

@section('content')
<div class="mb-6 flex justify-end">
    <x-ui.button :href="route('admin.crud.create', $resource)">+ Yangi</x-ui.button>
</div>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[680px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr>
                <th class="px-4 py-3">Nomi</th>
                <th class="px-4 py-3">Yaratilgan</th>
                <th class="px-4 py-3">Holat</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3 font-semibold">
                        {{ $item->title ?? $item->name ?? $item->question ?? $item->author_name ?? '#' . $item->id }}
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $item->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">
                        @if (isset($item->is_active) || isset($item->is_published) || isset($item->is_available))
                            <x-ui.badge :tone="($item->is_active ?? $item->is_published ?? $item->is_available) ? 'green' : 'rose'">
                                {{ ($item->is_active ?? $item->is_published ?? $item->is_available) ? 'Faol' : 'Yopiq' }}
                            </x-ui.badge>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3 text-xs font-semibold">
                            <a href="{{ route('admin.crud.edit', [$resource, $item->id]) }}" class="text-blue-600 dark:text-blue-400">Tahrir</a>
                            <form method="POST" action="{{ route('admin.crud.toggle', [$resource, $item->id]) }}">@csrf <button class="text-amber-500">Holat</button></form>
                            <form method="POST" action="{{ route('admin.crud.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('O‘chirilsinmi?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500">O‘chirish</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">Hozircha yozuvlar yo‘q.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<x-ui.pagination :paginator="$items" />
@endsection
