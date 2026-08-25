@extends('layouts.app')

@section('title', 'Bildirishnomalar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <div class="flex items-center justify-between">
        <x-app.page-header title="🔔 Bildirishnomalar" />
        <form method="POST" action="{{ route('notifications.mark-all') }}">
            @csrf
            <button class="text-sm font-semibold text-blue-600 dark:text-blue-400">Hammasini o‘qish</button>
        </form>
    </div>

    <div class="mt-6 grid gap-3">
        @forelse ($notifications as $notification)
            <div class="reveal flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900/60">
                <div>
                    <p class="text-sm font-medium">{{ $notification->data['message'] ?? 'Bildirishnoma' }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @if ($notification->unread())
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button class="rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white">O‘qish</button>
                    </form>
                @else
                    <span class="text-xs text-slate-300">✓</span>
                @endif
            </div>
        @empty
            <x-ui.empty icon="🔕" title="Bildirishnomalar yo‘q" />
        @endforelse
    </div>

    <x-ui.pagination :paginator="$notifications" />
</div>
@endsection
