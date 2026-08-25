@extends('layouts.admin')

@section('title', 'Activity log — Admin')
@section('admin-title', 'Faoliyat jurnali')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-3">
    <x-ui.input name="search" placeholder="Qidirish…" class="max-w-xs" />
    <x-ui.select name="action" :options="['auth' => 'Auth', 'job_apply' => 'Ariza', 'eco_complete' => 'Eko', 'coin_transfer' => 'Transfer', 'moderation' => 'Moderatsiya', 'admin_create' => 'Yaratish', 'telegram_broadcast' => 'Broadcast']" placeholder="Barcha amallar" />
    <x-ui.button variant="secondary">Filtr</x-ui.button>
</form>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[760px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">Vaqt</th><th class="px-4 py-3">Foydalanuvchi</th><th class="px-4 py-3">Amal</th><th class="px-4 py-3">Tavsif</th><th class="px-4 py-3">IP</th></tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $log->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-ui.badge>{{ $log->action }}</x-ui.badge></td>
                    <td class="px-4 py-3">{{ $log->description }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-400">{{ $log->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-ui.pagination :paginator="$logs" />
@endsection
