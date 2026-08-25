@extends('layouts.app')

@section('title', 'Arizalar — Biznes — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Arizalar" />

    <div class="mt-8 grid gap-3">
        @forelse ($applications as $application)
            <div class="reveal flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <p class="font-bold">{{ $application->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $application->job->title }} · {{ $application->created_at->format('d.m.Y') }}</p>
                    @if ($application->cover_letter)
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($application->cover_letter, 160) }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('business.applications.status', $application) }}" class="flex gap-2">
                    @csrf
                    <x-ui.select name="status" :options="['pending' => 'Yuborilgan', 'viewed' => 'Ko‘rildi', 'shortlisted' => 'Qisqa ro‘yxat', 'accepted' => 'Qabul', 'rejected' => 'Rad']" :value="$application->status->value" class="!w-44 !py-1.5" />
                    <button class="rounded-lg bg-blue-600 px-3 text-xs font-bold text-white">OK</button>
                </form>
            </div>
        @empty
            <x-ui.empty icon="📄" title="Arizalar yo‘q" />
        @endforelse
    </div>
</div>
@endsection
