@extends('layouts.app')

@section('title', 'Biznes paneli — AMBARELLA')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold">{{ $profile->company_name }}</h1>
            @if ($profile->isVerified())
                <x-ui.badge tone="green">✅ Tasdiqlangan biznes</x-ui.badge>
            @else
                <x-ui.badge tone="amber">⏳ Tekshiruv kutilmoqda</x-ui.badge>
            @endif
        </div>
        <div class="flex gap-2">
            <x-ui.button :href="route('business.jobs.create')">+ Ish e’loni</x-ui.button>
            <x-ui.button :href="route('business.profile')" variant="secondary">Profil</x-ui.button>
        </div>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Ishlar" :value="$stats['total']" icon="💼" tone="blue" />
        <x-ui.stat label="Faol" :value="$stats['active']" icon="✅" tone="green" />
        <x-ui.stat label="Arizalar" :value="$stats['applications']" icon="📄" tone="violet" />
        <x-ui.stat label="Kutilayotgan" :value="$stats['pending']" icon="⏳" tone="amber" />
    </div>

    <x-ui.card title="So‘nggi arizalar" class="mt-8">
        <div class="grid gap-3">
            @forelse ($latestApplications as $application)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-4 text-sm dark:border-slate-700">
                    <div>
                        <p class="font-semibold">{{ $application->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $application->job->title }} · {{ $application->created_at->diffForHumans() }}</p>
                    </div>
                    <x-ui.badge :tone="match ($application->status->value) { 'pending' => 'amber', 'accepted' => 'green', 'rejected' => 'rose', default => 'blue' }">{{ $application->status->label() }}</x-ui.badge>
                </div>
            @empty
                <p class="text-sm text-slate-400">Arizalar hali kelmadi.</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection
