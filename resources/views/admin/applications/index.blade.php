@extends('layouts.admin')

@section('title', 'Arizalar — Admin')
@section('admin-title', 'Ish arizalari')

@section('content')
<div class="mb-6 flex justify-end">
    <x-ui.button :href="route('admin.export.applications')" variant="secondary">CSV</x-ui.button>
</div>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[820px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">Nomzod</th><th class="px-4 py-3">Ish</th><th class="px-4 py-3">Holat</th><th class="px-4 py-3">Sana</th><th class="px-4 py-3 text-right">Amal</th></tr>
        </thead>
        <tbody>
            @foreach ($applications as $application)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $application->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $application->user->email }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $application->job?->title }}<br><span class="text-xs text-slate-400">{{ $application->job?->company }}</span></td>
                    <td class="px-4 py-3"><x-ui.badge :tone="match ($application->status->value) { 'pending' => 'amber', 'viewed' => 'blue', 'shortlisted' => 'violet', 'accepted' => 'green', default => 'rose' }">{{ $application->status->label() }}</x-ui.badge></td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $application->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.applications.status', $application) }}" class="flex justify-end gap-2">
                            @csrf
                            <x-ui.select name="status" :options="['pending' => 'Yuborilgan', 'viewed' => 'Ko‘rildi', 'shortlisted' => 'Qisqa ro‘yxat', 'accepted' => 'Qabul', 'rejected' => 'Rad']" :value="$application->status->value" class="!w-40 !py-1.5" />
                            <button class="rounded-lg bg-blue-600 px-3 text-xs font-bold text-white">OK</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-ui.pagination :paginator="$applications" />
@endsection
