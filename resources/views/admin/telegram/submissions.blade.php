@extends('layouts.admin')

@section('title', 'Telegram arizalar — Admin')
@section('admin-title', 'Telegram arizalar')

@section('content')
<div class="grid gap-4">
    @forelse ($submissions as $submission)
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
            <div>
                <p class="font-semibold">{{ $submission->user->name }} <span class="text-xs font-normal text-slate-400">({{ $submission->user->email }})</span></p>
                <p class="mt-1 text-sm">{{ $submission->payload_text }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $submission->task?->title ?? $submission->type }} · {{ $submission->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.badge :tone="match ($submission->status) { 'approved' => 'green', 'rejected' => 'rose', default => 'amber' }">{{ $submission->status }}</x-ui.badge>
                @if ($submission->status === 'pending')
                    <form method="POST" action="{{ route('admin.telegram.submissions.approve', $submission) }}">@csrf <x-ui.button variant="success">Tasdiqlash</x-ui.button></form>
                    <form method="POST" action="{{ route('admin.telegram.submissions.reject', $submission) }}">@csrf <x-ui.button variant="danger">Rad</x-ui.button></form>
                @endif
            </div>
        </div>
    @empty
        <x-ui.empty icon="🤖" title="Arizalar yo‘q" />
    @endforelse
</div>
<x-ui.pagination :paginator="$submissions" />
@endsection
