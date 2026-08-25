@extends('layouts.app')

@section('title', 'Arizalarim — AMBARELLA')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <x-app.page-header title="📄 Arizalarim" description="Yuborilgan arizalar va holatlari." />

    <div class="mt-8 grid gap-3">
        @forelse ($applications as $application)
            <div class="reveal flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <a href="{{ route('jobs.show', $application->job) }}" class="font-bold hover:text-blue-600 dark:hover:text-blue-400">{{ $application->job?->title }}</a>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $application->job?->company }} · {{ $application->created_at->format('d.m.Y') }}</p>
                </div>
                <x-ui.badge :tone="match ($application->status->value) {
                    'pending' => 'amber', 'viewed' => 'blue', 'shortlisted' => 'violet',
                    'accepted' => 'green', default => 'rose' }">{{ $application->status->label() }}</x-ui.badge>
            </div>
        @empty
            <x-ui.empty icon="📄" title="Arizalar yo‘q" message="Ish e’lonlaridan ariza yuboring" />
        @endforelse
    </div>

    <x-ui.pagination :paginator="$applications" />
</div>
@endsection
