@extends('layouts.admin')

@section('title', 'Dashboard — Admin')
@section('admin-title', 'Dashboard')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-ui.stat label="Foydalanuvchilar" :value="number_format($stats['users'])" icon="👥" tone="blue" />
    <x-ui.stat label="Yangi (7 kun)" :value="$stats['users_new_week']" icon="✨" tone="violet" />
    <x-ui.stat label="Faol ishlar" :value="$stats['jobs_active']" icon="💼" tone="green" />
    <x-ui.stat label="Kutilayotgan arizalar" :value="$stats['applications_pending']" icon="📄" tone="amber" />
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <x-ui.card title="Moderatsiya navbati">
        <div class="grid gap-2 text-sm">
            <div class="flex justify-between rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700"><span>📝 Blog maqolalari</span><strong>{{ $pending['blog_posts'] }}</strong></div>
            <div class="flex justify-between rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700"><span>🌱 Eko arizalar</span><strong>{{ $pending['eco'] }}</strong></div>
            <div class="flex justify-between rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700"><span>📄 Ish arizalari</span><strong>{{ $pending['applications'] }}</strong></div>
            <div class="flex justify-between rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700"><span>✉️ Murojaatlar</span><strong>{{ $pending['contacts'] }}</strong></div>
        </div>
    </x-ui.card>

    <x-ui.card title="So‘nggi faoliyat">
        <div class="grid gap-3 text-sm">
            @forelse ($recent_activities as $log)
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $log->description ?? $log->action }}</p>
                        <p class="text-xs text-slate-400">{{ $log->user?->name ?? 'Tizim' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-mono text-slate-400">{{ $log->action }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Faoliyat yo‘q.</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection
