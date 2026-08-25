@extends('layouts.admin')

@section('title', 'Ish e’lonlari — Admin')
@section('admin-title', 'Ish e’lonlari')

@section('content')
<div class="mb-6 flex justify-end">
    <x-ui.button :href="route('admin.jobs.create')">+ Yangi e’lon</x-ui.button>
</div>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[760px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">E’lon</th><th class="px-4 py-3">Tur</th><th class="px-4 py-3">Arizalar</th><th class="px-4 py-3">Holat</th><th class="px-4 py-3 text-right">Amallar</th></tr>
        </thead>
        <tbody>
            @foreach ($jobs as $job)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $job->title }}</p>
                        <p class="text-xs text-slate-400">{{ $job->company }} · {{ $job->location }}</p>
                    </td>
                    <td class="px-4 py-3"><x-ui.badge>{{ $job->type->label() }}</x-ui.badge></td>
                    <td class="px-4 py-3">{{ $job->applications_count }}</td>
                    <td class="px-4 py-3">@if ($job->is_active) <x-ui.badge tone="green">Faol</x-ui.badge> @else <x-ui.badge tone="rose">Yopiq</x-ui.badge> @endif</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3 text-xs font-semibold">
                            <a href="{{ route('admin.jobs.edit', $job) }}" class="text-blue-600 dark:text-blue-400">Tahrir</a>
                            <form method="POST" action="{{ route('admin.jobs.toggle', $job) }}">@csrf <button class="{{ $job->is_active ? 'text-amber-500' : 'text-emerald-500' }}">{{ $job->is_active ? 'Yopish' : 'Ochish' }}</button></form>
                            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('O‘chirilsinmi?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500">O‘chirish</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-ui.pagination :paginator="$jobs" />
@endsection
